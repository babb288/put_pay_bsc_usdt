<?php

namespace app\task\callback;

use app\task\model\Merchant;
use think\queue\Job;
use app\task\model\Apply;

class Pay
{

    public function __construct(
        private Apply $apply,
        private Merchant $merchant
    ){}

    public function fire(Job $job , $data)
    {

        $params = [
            'merchant_order' => $data['merchant_order'],
            'address'        => $data['address'],
            'price'          => $data['price'],
            'body'           => $data['body'],
            'network'        => $data['network'],
            'type'           => $data['type'],
            'status'         => 1,
            'txid'           => $data['txid'],
            'update_time'    => $data['update_time']
        ];

        $merchant = $this->merchant->where('username',$data['username'])->find();

        $params['uid'] = $merchant['uid'];

        $params['sign'] = $this->generateSign($params,$merchant->key);

        $resp = $this->curlpost($data['notify_url'], $params);

        if($resp === 'OK'){

            $this->apply
                ->where('merchant_order', $data['merchant_order'])
                ->where('username',$data['username'])
                ->update(['status'=>1]);

            $job->delete();
        }else{
            $job->release(3);
        }


    }

    public function failed($data)
    {
        $this->apply->where(['username' => $data['username'],'merchant_order'=>$data['merchant_order']])->update(['status' => 3]);
    }


    private function generateSign($params,$secretkey): string
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

    private function curlpost($url,$data): bool|string
    {
        $ch = curl_init();
        $params[CURLOPT_URL] = $url;    //请求url地址
        $params[CURLOPT_HEADER] = FALSE; //是否返回响应头信息
        $params[CURLOPT_SSL_VERIFYPEER] = false;
        $params[CURLOPT_SSL_VERIFYHOST] = false;
        $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
        $params[CURLOPT_POST] = true;
        $params[CURLOPT_POSTFIELDS] = http_build_query($data);
        $params[CURLOPT_HTTPHEADER]= array('Content-Type: application/x-www-form-urlencoded');//array('Content-Type: application/json','Content-Length: ' . strlen($data));
        curl_setopt_array($ch, $params); //传入curl参数
        $content = curl_exec($ch); //执行
        curl_close($ch); //关闭连接
        return $content;
    }
}