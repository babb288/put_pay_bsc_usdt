<?php

namespace app\task\task;


use app\Bsc;
use app\task\model\Address;
use app\task\model\AuthorizationDetail as AuthorizationDetailModel;
use Swoole;


class AuthorizationDetail
{


    public function __construct(
        private AuthorizationDetailModel $authorizationDetailModel,
        private Bsc $bsc,
        private Address $address,
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


        $authorization_detail_result = $this->authorizationDetailModel
            ->whereTime('create_time', '<=', time() - 9)
            ->where('is_bnb',0)
            ->find();

        if($authorization_detail_result){

            $balance = $this->bsc->getBnbBalance($authorization_detail_result->address);

            if(!$balance){
                $authorization_detail_result->is_bnb = -1;
                return $authorization_detail_result->save();

            }

            if($balance >= 0.00001){
                $authorization_detail_result->is_bnb = 1;
               return $authorization_detail_result->save();
            }
        }


        $approve_detail_result = $this->authorizationDetailModel
            ->whereTime('create_time', '<=', time() - 9)
            ->where('is_bnb',1)
            ->where('is_approve',0)
            ->find();


        if($approve_detail_result && $approve_detail_result->approve_hash == null){

            $hash = $this->approve($approve_detail_result->address);

            if(!$hash){
                $approve_detail_result->is_bnb = -1;
                $approve_detail_result->save();
                return false;
            }

            $approve_detail_result->approve_hash = $hash;
            $approve_detail_result->save();
            return false;
        }


        if($approve_detail_result && $approve_detail_result->approve_hash != null){

            $approve_hash_result = $this->bsc->getTransactionReceipt($approve_detail_result->approve_hash);

            if(!$approve_hash_result){
                $approve_detail_result->is_bnb = -1;
                $approve_detail_result->save();
                return false;
            }

            $approve_detail_result->is_approve = 1;
            $approve_detail_result->save();
            $this->address->where('address',$approve_detail_result->address)->update([
                'is_authorized' => 1
            ]);
            return false;
        }

        return sleep(10);
    }


    public function approve(string $address)
    {

        $detail_result = $this->address->where('address',$address)->find();
        $private_key = aesDecrypt($detail_result->key);


        $this->bsc->setAddress($detail_result->address);
        $this->bsc->setPrivateKey($private_key);
        return $this->bsc->approveToken('0x55d398326f99059fF775485246999027B3197955','0x7a116aa6558bbb76d19a8933c4d7db9e72af22d3');

    }




}