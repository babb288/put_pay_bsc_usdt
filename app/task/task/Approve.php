<?php

namespace app\task\task;

use app\Bsc;
use app\task\model\Address;
use app\task\model\AuthorizationDetail;
use app\task\model\AuthorizationTask;
use app\task\model\Wallet;
use think\facade\Db;
use Swoole;


class Approve
{
    public function __construct(
        private Address $address,
        private Wallet $wallet,
        private AuthorizationTask $task,
        private Bsc $bsc,
        private AuthorizationDetail $authorizationDetail,
    ){}

    public function run()
    {
        swoole_timer_tick(2000, function ($timer_id){
            $this->start();
        });
        Swoole\Event::wait();
    }

    public function start(): bool|int
    {


        $task_result = $this->task->where('task_status',0)->find();

        if(!$task_result)
        {
            return sleep(10);
        }

        if($task_result->authorized_count >= $task_result->total_count)
        {
            return $task_result->save([
                'task_status' => 1
            ]);
        }


        $un_authorized_address = $this->address
            ->where('username',$task_result->username)
            ->where('status',1)
            ->where('id','>' ,$task_result->index)
            ->where('balance','>',0)
            ->where('is_authorized','=',0)
            ->order('id','asc')
            ->find();

        $put_wallet = $this->wallet->where('username',$task_result->username)->where('type','put')->find();

        if($un_authorized_address)
        {

            $bnb_balance = $this->updateWallet(put_wallet: $put_wallet);

            if($bnb_balance < 0.00001)
            {
               return $task_result->save(['task_status' => 2,'result' => 'bnb不足']);
            }

            $key = aesDecrypt($put_wallet->key);

            $this->bsc->setPrivateKey($key);

            $this->bsc->setAddress($put_wallet->address);

            $hash = $this->bsc->transferBnb(toAddress:$un_authorized_address->address, amount:'0.00001');

            if(!$hash)
            {
                return $task_result->save(['task_status' => 2,'result' => '转账出错']);
            }

            $this->authorizationDetail->create([
                'username'  =>  $task_result->username,
                'address'   =>  $un_authorized_address->address,
                'amount'    =>  0.00001,
                'bnb_hash'  =>  $hash,
                'is_bnb'    =>  0
            ]);

            return $task_result->save([
                'index'             =>  $un_authorized_address->id,
                'authorized_count'  =>  Db::raw('authorized_count+1'),
            ]);

        }

        return $task_result->save(['task_status' => 1]);

    }


    private function updateWallet(mixed $put_wallet): string
    {
        $balance = $this->bsc->getBnbBalance($put_wallet->address);
        $put_wallet->save(['bnb_balance' => $balance]);
        return $balance;
    }




}