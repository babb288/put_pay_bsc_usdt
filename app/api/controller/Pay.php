<?php

namespace app\api\controller;

use app\api\model\Apply;
use app\api\model\Merchant;
use app\api\model\Wallet;
use app\Request;
use think\facade\Db;
use think\facade\Queue;

class Pay
{




    public function __construct(
        private Request $request,
        private Merchant $merchant,
        private Apply $apply,
        private Wallet  $wallet
    ){}



    public function index(): \think\response\Json
    {

        $params = $this->request->param();

        if($params['type'] !== 'usdt'){
            return json(['code'=>-1,'msg'=>'该代币暂时不支持']);
        }

        if ($params['price'] <= 0) {
            return json(['code' => -1, 'msg' => '金额传入错误']);
        }


        $merchant_find_result = $this->merchant->where('uid',$params['uid'])->find();

        if(!$merchant_find_result){
            return json(array('code' => -1,'msg' => '用户不存在','data' => []));
        }

        if($merchant_find_result->balance < 1){
            return json(array('code' => -1,'msg' => '商户余额不足'));
        }

        $sign = $this->generateSign($params,$merchant_find_result->key);

        if($sign != $params['sign']){
            return json(array('code' => -1,'msg' => '签名数据错误'));
        }

        $order_find_result = $this->apply
            ->where('merchant_order',$params['merchant_order'])
            ->find();

        if($order_find_result){
            return json(['code'=>-1,'msg'=>'商户订单号已存在']);
        }


        if(!in_array($this->request->ip(), $merchant_find_result->ip_whitelist)){
            return json(['code' => -1 , 'msg' => '白名单ip验证失败'.$this->request->ip()]);
        }

        $pay_wallet_find_result = $this->wallet
            ->where('username',$merchant_find_result['username'])
            ->where('type','pay')
            ->find();

        if(!$pay_wallet_find_result){
            return json(array('code' => -1,'msg' => '代付钱包不存在'));
        }


        $time = date('YmdHis');
        $micro = substr(microtime(), 2, 3);
        $rand = mt_rand(10, 99);

        $system_order = 'P'.$time . $micro . $rand;


        Db::startTrans();
        try{
            $data = [
                'uid'           => $merchant_find_result->uid,
                'username'      => $merchant_find_result->username,
                'system_order'  => $system_order,
                'merchant_order'=> $params['merchant_order'],
                'network'       => $params['network'],
                'type'          => $params['type'],
                'pay_address'   => $pay_wallet_find_result->address,
                'address'       => $params['address'],
                'price'         => $params['price'],
                'body'          => $params['body'],
                'notify_url'    => $params['notify_url'],
                'status'        => 0,
                'create_time'   => time(),
                'txid'          => '',
                'update_time'   => 0
            ];
            $this->apply->create($data);

            $merchant_find_result->save([
                'balance' => Db::raw('balance-1'),
            ]);
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            return json(array('code' => -1,'msg' => '系统繁忙,请稍后在试'));
        }

        Queue::push('\app\task\callback\Apply',$data,'ApplyTransfer');

        return json(array('code'=>1,'msg'=>'success','system_order'=>$system_order));

    }

    function generateSign(array $params,$secretkey): string
    {
        ksort($params);
        $string = [];
        foreach ($params as $key => $value) {
            if ($key == 'sign') continue;
            $string[] = $key . '=' . $value;
        }
        $sign = (implode('&', $string)) . '&key=' . $secretkey;

        return md5($sign);
    }


}