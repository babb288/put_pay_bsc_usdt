<?php

namespace app\task\service;

use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\Utils;
use Exception;


/**
 * EVM Transfer日志扫描服务
 * 使用 web3p/web3.php 库扫描ERC20代币的Transfer事件
 */
class EvmTransferScanner
{
    /**
     * Web3实例
     * @var Web3
     */
    private Web3 $web3;

    /**
     * Transfer事件签名
     * @var string
     */
    private string $transferEventSignature = 'Transfer(address,address,uint256)';

    /**
     * Transfer事件主题哈希
     * @var string
     */
    private string $transferTopic;

    /**
     * 构造函数
     * @param string $rpcUrl RPC节点地址，例如: https://bsc-dataseed.binance.org/
     * @param float $timeout 请求超时时间（秒），默认30秒
     */
    public function __construct(string $rpcUrl = '', float $timeout = 30.0)
    {
        $rpcUrl = $rpcUrl ?: config('bsc.https');

        $provider = new HttpProvider($rpcUrl, $timeout);
        $this->web3 = new Web3($provider);
        
        // 计算Transfer事件的主题哈希
        // Utils::sha3 已经返回带 '0x' 前缀的字符串，不需要再加
        $this->transferTopic = Utils::sha3($this->transferEventSignature);
    }

    /**
     * 扫描指定区块范围的Transfer日志
     * @param int $fromBlock 起始区块号
     * @param int|string $toBlock 结束区块号（可以是数字或'latest'）
     * @param string|null $contractAddress 合约地址（可选，不传则扫描所有合约）
     * @return array Transfer日志数组
     * @throws Exception
     */
    public function scanTransferLogs(int $fromBlock, int|string $toBlock = 'latest', string $contractAddress = null): array
    {
        // 构建 filter 对象，确保格式正确
        $filter = [];
        
        // fromBlock 必须是有效的 hex 字符串或 tag
        if (is_numeric($fromBlock)) {
            $filter['fromBlock'] = $this->toHex($fromBlock);
        } else {
            $filter['fromBlock'] = $fromBlock;
        }
        
        // toBlock 可以是 hex 字符串或 tag (latest, earliest, pending)
        if (is_numeric($toBlock)) {
            $filter['toBlock'] = $this->toHex($toBlock);
        } else {
            $filter['toBlock'] = $toBlock;
        }
        
        // topics 数组，Transfer 事件的签名
        $filter['topics'] = [
            $this->transferTopic
        ];

        // 如果指定了合约地址，添加地址过滤
        if ($contractAddress) {
            $filter['address'] = $this->normalizeAddress($contractAddress);
        }

        $transfers = [];
        $error = null;

        // 使用回调方式获取日志
        // web3p/web3.php 的 getLogs 方法期望第一个参数是 filter 数组
        $this->web3->eth->getLogs($filter, function ($err, $logs) use (&$transfers, &$error) {
            if ($err !== null) {
                $error = $err;
                return;
            }

            if (is_array($logs)) {
                foreach ($logs as $log) {
                    $transfer = $this->parseTransferLog($log);
                    if ($transfer) {
                        $transfers[] = $transfer;
                    }
                }
            }
        });

        if ($error !== null) {
            throw new Exception('RPC错误: ' . (is_string($error) ? $error : $error->getMessage()));
        }

        return $transfers;
    }

    /**
     * 扫描最新区块的Transfer日志
     * @param int $blockCount 扫描的区块数量
     * @param string|null $contractAddress 合约地址（可选）
     * @return array Transfer日志数组
     * @throws Exception
     */
    public function scanLatestBlocks($blockCount = 1, $contractAddress = null)
    {
        $latestBlock = $this->getLatestBlockNumber();
        $fromBlock = $latestBlock - $blockCount + 1;
        
        return $this->scanTransferLogs($fromBlock, $latestBlock, $contractAddress);
    }

    /**
     * 解析Transfer日志
     * @param object $log 原始日志对象
     * @return array|null 解析后的Transfer数据
     */
    private function parseTransferLog($log)
    {
        if (!isset($log->topics) || !is_array($log->topics) || count($log->topics) < 3) {
            return null;
        }

        // Transfer事件: Transfer(address indexed from, address indexed to, uint256 value)
        // topics[0] = Transfer事件签名
        // topics[1] = from地址（indexed）
        // topics[2] = to地址（indexed）
        // data = value（uint256）

        $from = $this->hexToAddress($log->topics[1]);
        $to = $this->hexToAddress($log->topics[2]);
        $value = $this->hexToDecimal($log->data ?? '0x0');

        return [
            'contract'          => $this->normalizeAddress($log->address ?? ''),
            'from'              => $from,
            'to'                => $to,
            'value'             => $value,
            'valueHex'          => $log->data ?? '0x0',
            'blockNumber'       => isset($log->blockNumber) ? hexdec($log->blockNumber) : 0,
            'blockHash'         => $log->blockHash ?? '',
            'transactionHash'   => $log->transactionHash ?? '',
            'transactionIndex'  => isset($log->transactionIndex) ? hexdec($log->transactionIndex) : 0,
            'logIndex'          => isset($log->logIndex) ? hexdec($log->logIndex) : 0,
            'removed'           => $log->removed ?? false,
        ];
    }

    /**
     * 获取最新区块号
     * @return int
     * @throws Exception
     */
    public function getLatestBlockNumber(): int
    {
        $blockNumber = null;
        $error = null;

        $this->web3->eth->blockNumber(function ($err, $block) use (&$blockNumber, &$error) {

            if ($err !== null) {
                $error = $err;
                return;
            }

            // $block 是 BigInteger 对象，需要转换为十进制
            // toString() 方法默认返回十进制字符串，直接转换为整数即可
            $blockNumber = (int)$block->toString();
        });

        if ($error !== null) {
            throw new Exception('RPC错误: ' . (is_string($error) ? $error : $error->getMessage()));
        }

        if ($blockNumber === null) {
            throw new Exception('无法获取区块号');
        }

        return $blockNumber;
    }

    /**
     * 将数字转换为十六进制
     * @param int $number
     * @return string
     */
    private function toHex($number): string
    {
        // 确保返回有效的 hex 格式，即使是 0 也要返回 '0x0'
        $hex = dechex($number);
        return '0x' . $hex;
    }

    /**
     * 将十六进制地址转换为标准地址格式
     * @param string $hex
     * @return string
     */
    private function hexToAddress($hex): string
    {
        // 移除0x前缀，取后40个字符（20字节）
        $hex = str_replace('0x', '', $hex);
        $hex = substr($hex, -40);
        return '0x' . strtolower($hex);
    }

    /**
     * 将十六进制转换为十进制
     * @param string $hex
     * @return string
     */
    private function hexToDecimal($hex): string
    {
        // 处理大数，使用BCMath扩展
        if (extension_loaded('bcmath')) {
            return $this->hexToDecimalBCMath($hex);
        }
        
        // 如果没有bcmath，尝试直接转换（可能溢出）
        return (string)hexdec($hex);
    }

    /**
     * 使用BCMath将十六进制转换为十进制（支持大数）
     * @param string $hex
     * @return string
     */
    private function hexToDecimalBCMath($hex): string
    {
        $hex = str_replace('0x', '', strtolower($hex));
        $dec = '0';
        $len = strlen($hex);
        
        for ($i = 0; $i < $len; $i++) {
            $dec = bcmul($dec, '16', 0);
            $dec = bcadd($dec, (string)hexdec($hex[$i]), 0);
        }
        
        return $dec;
    }

    /**
     * 标准化地址格式
     * @param string $address
     * @return string
     */
    private function normalizeAddress($address)
    {
        $address = str_replace('0x', '', $address);
        $address = strtolower($address);
        return '0x' . $address;
    }

    /**
     * 格式化代币数量（根据小数位数）
     * @param string $value 原始值（wei单位）
     * @param int $decimals 小数位数，默认18
     * @return string 格式化后的数量（截断到2位小数，不四舍五入）
     */
    public function formatTokenAmount($value, $decimals = 18)
    {
        if (!extension_loaded('bcmath')) {
            // 如果没有bcmath，简单处理
            $divisor = pow(10, $decimals);
            $amount = $value / $divisor;
            // 截断到2位小数（不四舍五入）
            return number_format(floor($amount * 100) / 100, 2, '.', '');
        }

        // 先除以10^decimals，保留足够的小数位
        $divisor = bcpow('10', (string)$decimals, 0);
        $amount = bcdiv($value, $divisor, $decimals);
        
        // 截断到2位小数（不四舍五入）
        // 方法：找到小数点位置，截取前2位小数
        if (strpos($amount, '.') !== false) {
            $parts = explode('.', $amount);
            $integerPart = $parts[0];
            $decimalPart = substr($parts[1] ?? '', 0, 2); // 截取前2位，不四舍五入
            $decimalPart = str_pad($decimalPart, 2, '0', STR_PAD_RIGHT); // 不足2位补0
            return $integerPart . '.' . $decimalPart;
        } else {
            // 没有小数部分，返回整数加.00
            return $amount . '.00';
        }
    }

    /**
     * 获取Web3实例（用于高级操作）
     * @return Web3
     */
    public function getWeb3()
    {
        return $this->web3;
    }
}

