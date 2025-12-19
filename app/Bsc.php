<?php

namespace app;

use Elliptic\EC;
use kornrunner\Keccak;
use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\Utils;
use Web3\Contract;
use Web3p\EthereumTx\Transaction;

class Bsc
{

    private Web3 $web3;

    private string $rpcUrl;
    private string $privateKey;
    private string $address;

    private int $gas = 21000;

    private float $gasPrice = 0.1;

    public function __construct()
    {
        $this->rpcUrl = config('bsc.https');
        $provider = new HttpProvider( $this->rpcUrl,40);
        $this->web3 = new Web3($provider);
    }

    public function setPrivateKey(string $privateKey): static
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function generateAddressAndPrivateKey(): array
    {
        $privateKeyBytes = random_bytes(32);
        $privateKeyHex = '0x' . bin2hex($privateKeyBytes);
        $ec = new EC('secp256k1');
        $keyPair = $ec->keyFromPrivate($privateKeyHex, 'hex');
        $publicKeyHex = $keyPair->getPublic(false, 'hex');
        $publicKeyBytes = hex2bin($publicKeyHex);
        $publicKeyBytes = substr($publicKeyBytes, 1);
        $hash = Keccak::hash($publicKeyBytes, 256);
        $address = '0x' . substr($hash, -40);
        return [
            'privateKey' => $privateKeyHex,
            'address' => strtolower($address)
        ];
    }

    public function getBnbBalance(string $address): string|null
    {

        $eth = $this->web3->eth;

        $balance = null;

        $eth->getBalance($address, function ($err, $result) use (&$balance) {
            if ($err !== null) {
               return ;
            }
            $balance =  bcdiv($result->value,'1000000000000000000', 18);
        });
        return $balance;
    }


    public function getTokenBalance(string $tokenContract, string $address, int $decimals = 18): string
    {

        $abi = '[{"constant":true,"inputs":[{"name":"_owner","type":"address"}],"name":"balanceOf","outputs":[{"name":"balance","type":"uint256"}],"type":"function"}]';
        
        $contract = new Contract($this->web3->provider, $abi);
        $contract->at($tokenContract);
        $balance = null;
        $contract->call('balanceOf', $address, function ($err, $result) use (&$balance) {
            if ($err !== null) {
               return ;
            }
            $balance =  bcdiv($result['balance']->value,'1000000000000000000', 18);
        });
        if ($balance === null) {
            return '0';
        }
        return $balance;
    }


    public function approveToken(string $token,string $address)
    {

        $gas = 70000;
        $gasPrice = 0.1;

        $transaction = $this->extracted(data:[
            'chainId'   =>  56,
            'from'      =>  $this->address,
            'to'        =>  $token,
            'nonce'     =>  Utils::toHex((int)$this->getNonce($this->address),true),
            'gas'       =>  Utils::toHex($gas,true),
            'gasPrice'  =>  utils::toHex(Utils::toWei((string)$gasPrice, 'gwei'),true),
            'data'      =>  '0x095ea7b3'.$this->str_pad_64(substr($address,2)).$this->str_pad_64('fffffffffffffffffffffffffff'),
            'value'     =>   '0x'
        ]);


        $signedTransaction = $transaction->sign(privateKey:$this->privateKey);

        $transactionResult = null;

        $this->web3->eth->sendRawTransaction('0x'.$signedTransaction,function($err,$result) use(&$transactionResult) {

            if(!$err){
                $transactionResult = $result;
            }
        });

        return $transactionResult;

    }

    public function getTransactionReceiptConnect(string $hash)
    {
        $receipt = null;

        $this->web3->eth->getTransactionReceipt($hash,function($err,$result) use(&$receipt) {

            if($err !== null) {
                echo "RPC error: " . $err->getMessage();
                return ;
            }

            $receipt = $result;
        });

        return $receipt;
    }
    public function getTransactionReceipt(string $hash)
    {
        $receipt = null;

        $this->web3->eth->getTransactionReceipt($hash,function($err,$result) use(&$receipt) {

            if($err !== null) {
                echo "RPC error: " . $err->getMessage();
                return ;
            }

            if ($result === null) {
                echo "交易未确认（pending）";
                return;
            }


            if (isset($result->removed) && $result->removed === true) {
                echo "交易被区块重组回滚（removed=true）";
                return;
            }



            $status = hexdec($result->status);

            if($result && $status === 0) {
                if (empty($result->logs)) {
                    $receipt = false;
                    return ;
                }
            }

            if($status === 1){
                $receipt = true;
            }
        });

        return $receipt;

    }


    public function transferBnb(string $toAddress, string $amount): string|null
    {
        $transaction = $this->extracted(data:[
            'chainId'   =>  56,
            'from'      =>  $this->address,
            'to'        =>  $toAddress,
            'nonce'     =>  Utils::toHex((int)$this->getNonce($this->address),true),
            'gas'       =>  Utils::toHex($this->gas,true),
            'gasPrice'  =>  utils::toHex(Utils::toWei((string)$this->gasPrice, 'gwei'),true),
            'value'     =>  utils::toHex((int)bcmul($amount, '1000000000000000000', 0),true),
        ]);


        $signedTransaction = $transaction->sign(privateKey:$this->privateKey);


        $transactionResult = null;

        $this->web3->eth->sendRawTransaction('0x'.$signedTransaction,function($err,$result) use(&$transactionResult) {
            if(!$err){
                $transactionResult = $result;
            }
        });

        return $transactionResult;
    }


    public function transferToken(string $token,string $address,string $amount)
    {

        $gas = 60000;
        $gasPrice = 0.1;

        $transaction = $this->extracted(data:[
            'chainId'   =>  56,
            'from'      =>  $this->address,
            'to'        =>  $token,
            'nonce'     =>  Utils::toHex((int)$this->getNonce($this->address),true),
            'gas'       =>  Utils::toHex($gas,true),
            'gasPrice'  =>  utils::toHex(Utils::toWei((string)$gasPrice, 'gwei'),true),
            'data'      =>  '0xa9059cbb'.
                $this->str_pad_64(substr($address,2)).
                $this->str_pad_64(utils::toHex((int)bcmul($amount, '1000000000000000000', 0))),
            'value'     =>   '0x'
        ]);

        $signedTransaction = $transaction->sign(privateKey:$this->privateKey);

        $transactionResult = null;

        $this->web3->eth->sendRawTransaction('0x'.$signedTransaction,function($err,$result) use(&$transactionResult) {
            if(!$err){
                $transactionResult = $result;
            }
        });

        return $transactionResult;

    }


    private function str_pad_64(string $str): string
    {
        return str_pad($str, 64, '0', STR_PAD_LEFT);
    }

    private function getNonce(string $address): string
    {
        $nonce = '';
        $this->web3->eth->getTransactionCount($address,function ($err,$result) use (&$nonce) {
            if(!$err){
                $nonce = $result->toString();
            }
        });
        return $nonce;
    }

    public function batchTransferFrom(
        string $contractAddress,
        string $tokenContract,
        array $froms,
        string $to,
        array $amounts,
        float $gasPrice = 0.1,
        int $gasLimit = 0,
    ): string|null {
        // ABI定义
        $abi = '[{"inputs":[{"internalType":"address","name":"token","type":"address"},{"internalType":"address[]","name":"froms","type":"address[]"},{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256[]","name":"amounts","type":"uint256[]"}],"name":"batchTransferFrom","outputs":[{"internalType":"bool","name":"success","type":"bool"}],"stateMutability":"nonpayable","type":"function"}]';
        
        // 确保amounts数组中的值都转换为wei格式（18位小数）的字符串
        $amountsFormatted = [];
        foreach ($amounts as $amount) {
            $amountWei = utils::toHex((int)bcmul((string)$amount, '1000000000000000000', 0),true);

            $amountsFormatted[] = $amountWei;
        }

        var_dump($amountsFormatted);
        exit();

        // 使用Contract类编码函数调用
        $contract = new Contract($this->web3->provider, $abi);
        $contract->at($contractAddress);
        $data = $contract->getData('batchTransferFrom', $tokenContract, $froms, $to, $amountsFormatted);

        if (is_string($data)) {
            $data =  '0x' . $data;
        }

        // 构建交易
        $transaction = $this->extracted(data: [
            'chainId'   => 56,
            'from'      => $this->address,
            'to'        => $contractAddress,
            'nonce'     => Utils::toHex((int)$this->getNonce($this->address), true),
            'gas'       => Utils::toHex($gasLimit, true),
            'gasPrice'  => Utils::toHex(Utils::toWei((string)$gasPrice, 'gwei'), true),
            'data'      => $data,
            'value'     => '0x'
        ]);

        // 签名交易
        $signedTransaction = $transaction->sign(privateKey: $this->privateKey);

        // 发送交易
        $transactionResult = null;
        $this->web3->eth->sendRawTransaction('0x' . $signedTransaction, function($err, $result) use (&$transactionResult) {
            if (!$err) {
                $transactionResult = $result;
            }
        });

        return $transactionResult;

    }

    /**
     * 预估批量转账的手续费
     * @param string $contractAddress 批量转账合约地址
     * @param string $tokenContract 代币合约地址
     * @param array $froms 源地址数组
     * @param string $to 目标地址
     * @param array $amounts 转账金额数组（decimal格式）
     * @param float $gasPrice 燃气价格（Gwei），默认0.1
     * @return array|null 返回数组包含 ['gas' => gas值, 'gasPrice' => gas价格(wei), 'fee' => 手续费(BNB)]
     */
    public function estimateBatchTransferFee(
        string $contractAddress,
        string $tokenContract,
        array $froms,
        string $to,
        array $amounts,
        float $gasPrice = 0.1
    ): array|null {

        // ABI定义
        $abi = '[{"inputs":[{"internalType":"address","name":"token","type":"address"},{"internalType":"address[]","name":"froms","type":"address[]"},{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256[]","name":"amounts","type":"uint256[]"}],"name":"batchTransferFrom","outputs":[{"internalType":"bool","name":"success","type":"bool"}],"stateMutability":"nonpayable","type":"function"}]';
        
        // 确保amounts数组中的值都转换为wei格式（18位小数）的字符串
        $amountsFormatted = [];
        foreach ($amounts as $amount) {
            $amountWei = utils::toHex((int)bcmul((string)$amount, '1000000000000000000', 0),true);
            $amountsFormatted[] = $amountWei;
        }

        $contract = new Contract($this->web3->provider, $abi);

        $contract->at($contractAddress);

        $gas = null;
        $error = null;

        $contract->estimateGas('batchTransferFrom', $tokenContract, $froms, $to, $amountsFormatted,
            ['from' => $this->address],
            function ($err, $result) use (&$gas, &$error) {

                if ($err !== null) {
                    $error = $err;
                    return;
                }

                if (is_object($result)) {
                    $gas = $result->toString();
                } else {
                    $gas = (string)$result;
                }
            }
        );

        if ($error !== null || $gas === null) {
            return null;
        }

        // 计算手续费：gas * gasPrice（转换为wei）
        $gasPriceWei = Utils::toWei((string)$gasPrice, 'gwei');
        $gasPriceStr = is_object($gasPriceWei) ? $gasPriceWei->toString() : (string)$gasPriceWei;

        // 手续费 = gas * gasPrice (wei单位)
        $feeWei = bcmul($gas, $gasPriceStr, 0);

        // 转换为BNB（除以10^18）
        $feeBnb = bcdiv($feeWei, '1000000000000000000', 18);

        return [
            'gas'          => $gas,
            'gasPrice'     => $gasPriceStr,
            'feeWei'       => $feeWei,
            'feeBnb'       => $feeBnb,
            'gasPriceGwei' => $gasPrice
        ];


    }

    private function extracted(array $data): Transaction
    {
        return new Transaction($data);
    }

}
