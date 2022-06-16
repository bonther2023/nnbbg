<?php

namespace App\HttpController\App;

use App\HttpController\BaseController;
use App\Model\Orders;
use App\Model\Pays;
use App\Utility\Pay;
use Carbon\Carbon;

class NotifyController extends BaseController
{

    public function yangyu(){
        $request = $this->request();
        $params = $request->getBody();
        $params = $this->parseData($params);
        //获取支付平台标识
        $name = 'yangyu';
        return $this->notify($params,$name);
    }

    public function rongyi(){
        $request = $this->request();
        $params = $request->getRequestParam();
        //获取支付平台标识
        $name = 'rongyi';
        return $this->notify($params,$name);
    }

    public function cang(){
        $request = $this->request();
        $params = $request->getRequestParam();
        //获取支付平台标识
        $name = 'cang';
        return $this->notify($params,$name);
    }

    public function minsen(){
        $request = $this->request();
        $params = $request->getRequestParam();
        //获取支付平台标识
        $name = 'minsen';
        return $this->notify($params,$name);
    }



    public function fuxi(){
        $request = $this->request();
        $params = $request->getBody();
        $params = $this->parseData($params);
        //获取支付平台标识
        $name = 'fuxi';
        return $this->notify($params,$name);
    }

    public function fuxiy(){
        $request = $this->request();
        $params = $request->getBody();
        $params = $this->parseData($params);
        //获取支付平台标识
        $name = 'fuxiy';
        return $this->notify($params,$name);
    }

    public function bossyun(){
        $request = $this->request();
        $params = $request->getBody()->__toString();
        $params = unjson($params);
        //获取支付平台标识
        $name = 'bossyun';
        return $this->notify($params,$name);
    }

    public function xinke(){
        $request = $this->request();
        $params = $request->getBody()->__toString();
        $params = unjson($params);
        //获取支付平台标识
        $name = 'xinke';
        return $this->notify($params,$name);
    }

    public function guazi(){
        $request = $this->request();
        $params = $request->getRequestParam();
        //获取支付平台标识
        $name = 'guazi';
        return $this->notify($params,$name);
    }


    protected function notify($params,$name){
        write_log($name);
        write_log($params);
        if(empty($params)){
            return $this->writeEcho('fail,params is empty');
        }
        //获取支付配置信息
        $config = Pays::create()->info($name);

        $pay = new Pay();
        //订单信息
        $orderFiled = $config['return_order'];

        $orderModel = Orders::create();
        $info = $orderModel->where('number', $params[$orderFiled])->order('created_at','desc')->get();
        if(!$info) return $this->writeEcho('fail,no order info');

        //如果订单状态为已支付状态，而支付平台未返回，则特殊处理一下
        if($info['status'] == Orders::STATUS_2){
            return $this->writeEcho($config['return_msg']);
        }

        //根据标识获取相应的支付订单回调状态
        if($config['return_ok_field'] && ($params[$config['return_ok_field']] != $config['return_ok_msg'])){
            return $this->writeEcho('fail,order status is failed');
        }

        //验签
        $check = $pay->check($params,$name,$config['pay_secret']);
        if(!$check) return $this->writeEcho('fail,check sign fail');

        //更新flow
        $this->queueFlow([
            'info' => [
                'aid' => 0,
                'cid' => $info['cid'],
                'date' => Carbon::parse($info['created_at'])->toDateString(),
                'system' => $info['system'],
                'money' => $info['money'],
                'mtype' => $info['mtype'],
                'oid' => $info['id'],
            ],
            'order' => true,
        ]);
        //更新report
        $this->queueReport([
            'info' => [
                'aid' => 0,
                'cid' => $info['cid'],
                'date' => Carbon::parse($info['created_at'])->toDateString(),
                'hour' => Carbon::parse($info['created_at'])->hour,
            ],
            'pay' => true,
        ]);
        //异步通知成功
        return $this->writeEcho($config['return_msg']);

    }


}
