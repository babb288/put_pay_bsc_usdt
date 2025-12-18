<?php

namespace app\task\service;

/**
 * EVM Transfer扫描器使用示例
 * 使用 web3p/web3.php 库
 */
class EvmTransferScannerExample
{
    /**
     * 示例：扫描指定区块范围的Transfer日志
     */
    public function exampleScanByBlockRange()
    {
        // 初始化扫描器（BSC主网）
        $scanner = new EvmTransferScanner('https://bsc-dataseed.binance.org/');
        
        try {
            // 扫描区块 30000000 到 30000010 的所有Transfer事件
            $transfers = $scanner->scanTransferLogs(30000000, 30000010);
            
            echo "找到 " . count($transfers) . " 个Transfer事件\n";
            
            foreach ($transfers as $transfer) {
                echo "合约: {$transfer['contract']}\n";
                echo "从: {$transfer['from']}\n";
                echo "到: {$transfer['to']}\n";
                echo "数量: {$transfer['value']} (原始值)\n";
                // 格式化代币数量（假设18位小数）
                $formatted = $scanner->formatTokenAmount($transfer['value'], 18);
                echo "格式化数量: {$formatted}\n";
                echo "区块号: {$transfer['blockNumber']}\n";
                echo "交易哈希: {$transfer['transactionHash']}\n";
                echo "---\n";
            }
        } catch (\Exception $e) {
            echo "错误: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 示例：扫描特定合约的Transfer日志
     */
    public function exampleScanByContract()
    {
        // USDT合约地址（BSC）
        $usdtContract = '0x55d398326f99059fF775485246999027B3197955';
        
        $scanner = new EvmTransferScanner('https://bsc-dataseed.binance.org/');
        
        try {
            // 扫描最新10个区块中USDT的Transfer事件
            $transfers = $scanner->scanLatestBlocks(10, $usdtContract);
            
            echo "找到 " . count($transfers) . " 个USDT Transfer事件\n";
            
            foreach ($transfers as $transfer) {
                $amount = $scanner->formatTokenAmount($transfer['value'], 18);
                echo "{$transfer['from']} -> {$transfer['to']}: {$amount} USDT\n";
            }
        } catch (\Exception $e) {
            echo "错误: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 示例：扫描最新区块
     */
    public function exampleScanLatest()
    {
        $scanner = new EvmTransferScanner('https://bsc-dataseed.binance.org/');
        
        try {
            // 获取最新区块号
            $latestBlock = $scanner->getLatestBlockNumber();
            echo "最新区块号: {$latestBlock}\n";
            
            // 扫描最新1个区块的所有Transfer事件
            $transfers = $scanner->scanLatestBlocks(1);
            
            echo "找到 " . count($transfers) . " 个Transfer事件\n";
        } catch (\Exception $e) {
            echo "错误: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 示例：扫描以太坊主网
     */
    public function exampleScanEthereum()
    {
        // 使用以太坊主网RPC
        $scanner = new EvmTransferScanner('https://eth.llamarpc.com');
        
        try {
            $latestBlock = $scanner->getLatestBlockNumber();
            echo "以太坊最新区块号: {$latestBlock}\n";
            
            // 扫描最新5个区块
            $transfers = $scanner->scanLatestBlocks(5);
            echo "找到 " . count($transfers) . " 个Transfer事件\n";
        } catch (\Exception $e) {
            echo "错误: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 示例：使用Web3实例进行高级操作
     */
    public function exampleAdvancedUsage()
    {
        $scanner = new EvmTransferScanner('https://bsc-dataseed.binance.org/');
        $web3 = $scanner->getWeb3();
        
        // 获取区块信息
        $web3->eth->getBlockByNumber('latest', false, function ($err, $block) {
            if ($err !== null) {
                echo "错误: " . $err->getMessage() . "\n";
                return;
            }
            
            echo "区块哈希: {$block->hash}\n";
            echo "区块号: " . hexdec($block->number) . "\n";
            echo "交易数量: " . count($block->transactions) . "\n";
        });
    }
}

