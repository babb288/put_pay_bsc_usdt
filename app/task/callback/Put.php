<?php

namespace app\task\callback;

use app\api\model\Order;
use app\task\model\Merchant;
use think\queue\Job;


class Put
{

    public function __construct(
        private Merchant $merchant,
        private Order $order,
    ){}

    public function fire(Job $job,$data): void
    {

        $params = [
            'bind_data'         => $data['bind_data'],
            'system_order'      => $data['system_order'],
            'price'             => $data['price'],
            'body'              => $data['body'],
            'network'           => $data['network'],
            'type'              => $data['type'],
            'status'            => 1,
            'txid'              => $data['txid'],
            'update_time'       => $data['update_time']
        ];

        $merchant = $this->merchant->where('username',$data['username'])->find();

        $params['uid'] = $merchant['uid'];

        $params['sign'] = $this->generateSign($params,$merchant->key);

        $res = $this->curlpost($data['notify_url'], $params);

        if($res === 'OK'){
            $this->order->where('system_order', $data['system_order'])->update(['status'=>1]);
            $job->delete();
        }else{
            $job->release(3);
        }

    }

    public function failed($data)
    {
        $this->order->where('system_order', $data['merchant_order'])->update(['status'=>3]);
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