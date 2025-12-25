<?php

namespace app\task\task;

use app\api\model\Address;
use app\api\model\Order;
use app\task\model\Settings;
use app\task\service\EvmTransferScanner;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Queue;
use Swoole;

class Block
{

    public function __construct(
        private EvmTransferScanner  $evmTransferScanner,
        private Settings  $settings,
        private Address $address,
        private Order $order,
    )
    {}


    public function run(): void
    {
        swoole_timer_tick(4000, function ($timer_id){
            $this->start();
        });
        Swoole\Event::wait();
    }


    public function start()
    {
        try {

            $settings = $this->getSettings();

            $to_block = $settings->last_block+$settings->last_num;

            $latest_block = $this->evmTransferScanner->getLatestBlockNumber();

            echo '最新区块'.$latest_block.PHP_EOL;

            if($to_block > $latest_block){
                $to_block = $latest_block;
            }

            $transfers = $this->evmTransferScanner->scanTransferLogs(
                fromBlock: $settings->last_block,
                toBlock: $to_block,
                contractAddress: '0x55d398326f99059ff775485246999027b3197955'
            );

            foreach ($transfers as $transfer) {

                if($transfer['removed']){
                    continue;
                }

                $transfer['amount'] = $this->evmTransferScanner->formatTokenAmount($transfer['value'], 2);
                $this->handle($transfer);
            }

            $this->settings->where('name','settings')->update(['last_block' => $to_block]);

            echo '从'.$settings->last_block."到".$to_block.'区块'.PHP_EOL;
            echo '------------------------------------'.PHP_EOL;

        } catch (\Exception $e) {
            echo "错误: " . $e->getMessage() . "\n";
        }
    }


    public function handle(array $log_array)
    {

        $from_address_result = $this->find_address_exits($log_array['from']);

        if($from_address_result){
            $is_hash = Cache::store('redis')->get(md5($log_array['transactionHash'].$log_array['from']));
            if(!$is_hash){
                echo $log_array['from'].'余额转出'.$log_array['amount'].PHP_EOL;
                try{
                    $from_address_result->save([
                        'balance'   =>  Db::raw('balance-'.$log_array['amount'])
                    ]);
                    Cache::store('redis')->set(md5($log_array['transactionHash'].$log_array['from']),$log_array['transactionHash'],3600);
                }catch(\Exception $e){
                    //添加异常记录
                }
            }
        }

        $to_address_result = $this->find_address_exits(address:$log_array['to']);

        if($to_address_result){
            $order_find_hash_result =  $this->find_hash_exits(hash:$log_array['transactionHash']);
            if(!$order_find_hash_result){
                echo $log_array['to']."收到USDT".$log_array['amount'].PHP_EOL;
                $to_address_result->save([
                    'balance'   =>  Db::raw('balance+'.$log_array['amount'])
                ]);
                $this->createOrder(log_array: $log_array,address_data: $to_address_result->toArray());
            }
        }

    }

    public function createOrder(array $log_array,array $address_data)
    {

        $data = [
            'username'          => $address_data['username'],
            'system_order'      => $this->generateOrderNo(),
            'bind_data'         => $address_data['bind_data'],
            'network'           => 'bsc',
            'type'              => 'usdt',
            'pay_address'       => $log_array['from'],
            'address'           => $log_array['to'],
            'notify_url'        => $address_data['callback_url'],
            'price'             => $log_array['amount'],
            'txid'              => $log_array['transactionHash'],
            'body'              => $address_data['body'],
            'status'            => 2,
            'update_time'       => time()
        ];

        $this->order->create($data);

        //回调queue
        Queue::push('\app\task\callback\Put',$data,'put');
    }

    private function generateOrderNo(): string
    {
        $time = date('YmdHis');
        $micro = substr(microtime(), 2, 3);
        $rand = mt_rand(10, 99);
        return 'C'.$time . $micro . $rand;
    }


    public function find_hash_exits(string $hash)
    {
        return $this->order->where('txid',$hash)->find();
    }


    public function find_address_exits(string $address)
    {
        return $this->address->where('address',$address)->find();
    }

    public function getSettings()
    {
        return $this->settings->where('name','settings')->find();
    }


}