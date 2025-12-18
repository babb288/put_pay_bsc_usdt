<?php

namespace app\task\callback;

use app\Bsc;
use app\task\model\Wallet;
use think\facade\Queue;
use think\queue\Job;
use app\task\model\Apply as ApplyModel;



class Apply
{


    public function __construct(
        private Bsc  $bsc,
        private Wallet $wallet,
        private ApplyModel $applyModel,
    ){}

    public function fire(Job $job, $data)
    {
        try{
            $job->delete();

            // 验证钱包是否存在
            $pay_wallet = $this->wallet
                ->where('username', $data['username'])
                ->where('type','pay')
                ->find();
            if (!$pay_wallet) {
                return false;
            }

            // 验证订单是否存在
            $apply_find_result = $this->applyModel->where('system_order', $data['system_order'])->find();

            if (!$apply_find_result) {
                return false;
            }

            // 配置BSC并执行转账
            $bsc = $this->bsc->setPrivateKey($this->aesDecrypt($pay_wallet->key))->setAddress($pay_wallet->address);
            $hash = $bsc->transferToken(
                token: '0x55d398326f99059fF775485246999027B3197955',
                address: $data['address'],
                amount: $data['price']
            );

            // 转账失败，更新订单状态
            if ($hash === null) {
                return $this->updateOrderStatus($apply_find_result, -1, 'bnb不足或余额不足');
            }

            // 重试获取交易回执
            $hash_result = $this->retry($hash);

            // 交易成功，更新订单状态并推送回调
            if ($hash_result === true) {
                $apply_find_result->save([
                    'result'    => '',
                    'txid'      => $hash,
                    'status'    => 2
                ]);
                return Queue::push('\app\task\callback\Pay', $apply_find_result->toArray(), 'pay');
            }

            // 交易失败（false或null），更新订单状态
            return $this->updateOrderStatus($apply_find_result, -1, 'bnb不足或余额不足');
        }catch (\Exception $e){
            echo '错误捕捉'.$e->getMessage().PHP_EOL;
        }

        return false;
    }

    /**
     * 更新订单状态
     */
    private function updateOrderStatus($order, int $status, string $result,string $hash = ''): bool
    {
        return $order->save([
            'result'    => $result,
            'txid'      => $hash,
            'status'    => $status
        ]);
    }


    public function failed()
    {
            
    }


    public function retry(string $hash): ?bool
    {
        $maxRetries = 3;
        $retryDelay = 2; // 秒
        
        for ($i = 0; $i <= $maxRetries; $i++) {
            $hash_result = $this->bsc->getTransactionReceipt($hash);
            
            // 如果不是null（是false或true），直接返回
            if ($hash_result !== null) {
                return $hash_result;
            }
            
            // 如果是null且还有重试次数，等待后继续重试
            if ($i < $maxRetries) {
                sleep($retryDelay);
            }
        }
        
        // 重试3次后还是null，返回null
        return null;
    }


    /**
     * AES解密
     */
    private function aesDecrypt(string $data): string|false
    {
        list($encryptedData, $iv) = explode("::", $data);
        return openssl_decrypt($encryptedData, 'AES-128-CBC', 'c99a11a53a3748269e3f86d7ac38df11', 0, hex2bin($iv));
    }


}