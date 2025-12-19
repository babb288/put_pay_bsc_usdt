<?php

namespace app\api\controller;


use app\api\model\Address;
use app\api\model\Merchant;
use app\Request;
use app\Bsc;


class Put
{

    public function __construct(
        private Request $request,
        private Merchant $merchant,
        private Address $address,
        private Bsc $bsc
    ){

    }

    public function index(): \think\response\Json
    {

        $params = $this->request->param();

        $merchant = $this->merchant->where('uid',$params['uid'])->find();

        if(!$merchant){
            return json(['code'=>-1,'msg'=>'商户uid不存在']);
        }

        if($merchant['status'] == 0){
            return json(['code'=>-1,'msg'=>'商户已被禁用,请联系后台']);
        }

        $sign = $this->generateSign($params,$merchant['key']);

        if($sign !== $params['sign'] ){
            return json(['code'=>-1,'msg'=>'sign签名错误']);
        }

        if($params['type'] !== 'usdt'){
            return json(['code'=>-1,'msg'=>'该代币暂时不支持']);
        }

        $address_find_result = $this->address
            ->where('bind_data',$params['bind_data'])
            ->where('username',$merchant['username'])
            ->where('status',1)
            ->find();

        if(!$address_find_result){
            $address_data = $this->bsc->generateAddressAndPrivateKey();
            $address_data['key'] = aesEncrypt($address_data['privateKey']);
            $address_data['username'] = $merchant['username'];
            $address_data['bind_data'] = $params['bind_data'];
            $address_data['callback_url'] = $params['notify_url'];
            $address_data['redirect_url'] = $params['redirect_url'];
            $address_data['body'] = $params['body'];

            $this->address->create($address_data);

            return json([
                'code'  => 1,
                'msg'   =>  'success',
                'data'  =>  [
                    'native_url' => '0.0.0.0',
                    'to_address' => $address_data['address'],
                ]
            ]);
        }

        return json([
            'code'=> 1,
            'msg' =>'success',
            'data'=>[
                'native_url' => '0.0.0.0',
                'to_address' => $address_find_result['address']
            ]
        ]);

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