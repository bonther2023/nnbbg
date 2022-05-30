<?php

namespace App\Utility;

use App\Model\Pays;
use EasySwoole\HttpClient\HttpClient;
use EasySwoole\RedisPool\RedisPool;

class Pay
{

    public function order($order,$func,$config)
    {
        switch ($func){
            case 'rongyi'://融易支付
                $orderInfo = [
                    'order_sn'   => $order['orderid'],//订单号
                    'money'      => $order['amount'],//订单金额
                    'goods_desc' => $order['subject'],//订单标题
                    'pay_code'   => $order['payment'] == 1 ? $config['payment_w'] : $config['payment_a'],//支付编码
                    'notify_url' => urlencode($order['notify']),//异步回调
                    'return_url' => urlencode($order['return']),//同步回调
                    'time'       => time(),
                ];
                $orderInfo['mch_id'] = $config['pay_number'];//商户号
                $orderInfo['sign']   = $this->sign($orderInfo,$config['pay_secret'],$func);//签名
                break;
            case 'minsen'://铭森支付
            case 'cang'://小苍支付
                $orderInfo = [
                    'pay_orderid'    => $order['orderid'],//订单号
                    'pay_amount'     => $order['amount'],//订单金额
                    'pay_applydate'  => date('Y-m-d H:i:s'),//订单时间
                    'pay_bankcode'   => $order['payment'] == 1 ? $config['payment_w'] : $config['payment_a'],//支付编码
                    'pay_notifyurl'  => $order['notify'],//异步回调
                    'pay_callbackurl'=> $order['return'],//同步回调
                ];
                $orderInfo['pay_memberid']   = $config['pay_number'];//商户号
                $orderInfo['pay_md5sign']    = $this->sign($orderInfo,$config['pay_secret'],$func);//签名
                $orderInfo['pay_productname']= $order['subject'];//订单标题
                break;
            case 'yangyu': //洋芋支付
            case 'fuxi': //伏羲支付
                $orderInfo = [
                    'fxddh'      => $order['orderid'], //订单号
                    'fxfee'      => number_format($order['amount'], 2), //订单金额
                    'fxpay'      => $order['payment'] == 1 ? $config['payment_w'] : $config['payment_a'], //支付编码
                    'fxnotifyurl'=> $order['notify'], //异步回调
                    'fxbackurl'  => $order['return'], //同步回调
                ];
                $orderInfo['fxid']  = $config['pay_number']; //商户号
                $orderInfo['fxdesc']= $order['subject'];//订单标题
                $orderInfo['fxip']  = $order['ip']; //ip
                $orderInfo['fxsign']= $this->sign($orderInfo, $config['pay_secret'], $func); //签名
                break;
            case 'bossyun': //BOSS云支付
                $orderInfo = [
                    'orderno'  => $order['orderid'],//订单号
                    'money'    => ($order['amount'] - mt_rand(0.1*100, 0.2*100) / 100)* 100,//订单金额
                    'type'     => $order['payment'] == 1 ? $config['payment_w'] : $config['payment_a'], //支付编码
                    'notifyurl'=> $order['notify'],//异步回调
                ];
                $orderInfo['channel']= $config['pay_number'];//商户号
                $orderInfo['attach'] = $order['subject'];
                $orderInfo['sign']   = $this->sign($orderInfo,$config['pay_secret'],$func);//签名
                break;
            case 'xinke': //鑫科支付
                $orderInfo = [
                    'order_id'  => $order['orderid'],//订单号
                    'amount'    => $order['amount'],//订单金额
                    'channel_id'=> $order['payment'] == 1 ? $config['payment_w'] : $config['payment_a'], //支付编码
                    'notify_url'=> $order['notify'],//异步回调
                ];
                $orderInfo['appid']= $config['pay_number'];//商户号
                $orderInfo['subject'] = $order['subject'];
                $orderInfo['sign']   = $this->sign($orderInfo,$config['pay_secret'],$func);//签名
                break;
            case 'guazi'://瓜子支付
                $orderInfo = [
                    'pay_orderid'    => $order['orderid'],//订单号
                    'pay_amount'     => $order['amount'],//订单金额
                    'pay_applydate'  => date('Y-m-d H:i:s'),//订单时间
                    'pay_bankcode'   => $order['payment'] == 1 ? $config['payment_w'] : $config['payment_a'],//支付编码
                    'pay_notifyurl'  => $order['notify'],//异步回调
                    'pay_callbackurl'=> $order['return'],//同步回调
                ];
                $orderInfo['pay_memberid']   = $config['pay_number'];//商户号
                $orderInfo['pay_md5sign']    = $this->sign($orderInfo,$config['pay_secret'],$func);//签名
                $orderInfo['pay_productname']= $order['subject'];//订单标题
                break;
            default:
                $orderInfo = [];
                break;
        }
        return [
            'orderInfo'   => $orderInfo,
            'orderMethod' => $config['pay_method'],
            'orderFormat' => $config['pay_format'],
            'orderPayUrl' => $config['pay_target']
        ];
    }


    /**
     * 生成签名结果，可以共用
     * @param $data
     * @param $secret
     * @param $func
     * @return string
     */
    private function sign($data, $secret, $func)
    {
        switch ($func) {
            case 'yangyu':
            case 'fuxi':
                $sign = md5($data['fxid'] . $data['fxddh'] . $data['fxfee'] . $data['fxnotifyurl'] . $secret);
                break;
            case 'rongyi':
                if(isset($data['sign'])) unset($data['sign']);
                ksort($data);
                $sign = md5(http_build_query($data).'&key='.$secret);
                break;
            case 'xinke':
                ksort($data);
                //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
                $md5str = "";
                foreach ($data as $key => $val) {
                    if (strtolower($key) != 'sign' && $key != 'attach' && $val != null && $val != '' && !is_array($val)) {
                        $md5str .= $key . "=" . $val . "&";
                    }
                }
                //把拼接后的字符串再与安全校验码直接连接起来并加密，获得签名结果
                $sign = md5($md5str."token=".$secret);
                break;
            default:
                ksort($data);
                //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
                $md5str = "";
                foreach ($data as $key => $val) {
                    if (strtolower($key) != 'sign' && $key != 'sign_type' && $val != null && $val != '' && !is_array($val)) {
                        $md5str .= $key . "=" . $val . "&";
                    }
                }
                //把拼接后的字符串再与安全校验码直接连接起来并加密，获得签名结果
                $sign = strtoupper(md5($md5str."key=".$secret));
                break;
        }
        return $sign;
    }



    public function check($params, $func, $secret)
    {
        $sign = $params['sign'] ?? '';//部分支付不是这个字段，在switch中自行重置
        switch($func){
            case 'yangyu':
            case 'fuxi':
                $sign  = $params['fxsign'];
                $check = md5($params['fxstatus'] . $params['fxid'] . $params['fxddh'] . $params['fxfee'] . $secret);
                break;
            default:
                $check = $this->sign($params, $secret, $func);
                break;
        }
        return $sign === $check;
    }


    public function send($order, $func){
        if($order['orderMethod'] == 'GET'){
            $order['orderPayUrl'] .= http_build_query($order['orderInfo']);
        }
        $request = new HttpClient($order['orderPayUrl']);
        //设置等待超时时间
        $request->setTimeout(120);
        //设置连接超时时间
        $request->setConnectTimeout(120);
        switch($func){
            case 'yangyu'://洋芋支付
            case 'fuxi'://伏羲支付
                $response = $request->post($order['orderInfo']);
                $body = $response->getBody();
                $result = unjson($body);
                if(isset($result) && $result['status'] == 1){
                    return ['status' => 0,'payurl' => $result['payurl']];
                }else{
                    write_log($func.'-error');
                    write_log($result);
                    $error = $result['msg'] ?? $response->getErrMsg();
                    return ['status' => 1,'msg' => $error];
                }
                break;
            case 'guazi'://瓜子支付
                $request->setContentTypeFormUrlencoded();
                $response = $request->post($order['orderInfo']);
                $body = $response->getBody();
                $result = unjson($body);
                if(isset($result) && $result['code'] == 200){
                    return ['status' => 0,'payurl' => $result['data']];
                }else{
                    $error = $result['msg'] ?? $response->getErrMsg();
                    return ['status' => 1,'msg' => $error];
                }
                break;
            case 'minsen'://铭森支付
            case 'cang'://小苍支付
                $request->setContentTypeFormUrlencoded();
                $response = $request->post($order['orderInfo']);
                $body = $response->getBody();
                $result = unjson($body);
                return ['status' => 0,'payurl' => $result];
                break;
            case 'rongyi'://融易支付
                $response = $request->post($order['orderInfo']);
                $body = $response->getBody();
                return ['status' => 0,'html' => $body];
                break;
            case 'bossyun'://BOSS支付
                $request->setContentTypeFormUrlencoded();
                $response = $request->post($order['orderInfo']);
                $body = $response->getBody();
                $result = unjson($body);
                if(isset($result) && $result['data']['result'] == true){
                    return ['status' => 0,'payurl' => $result['data']['pay_url']];
                }else{
                    write_log($func.'-error');
                    write_log($result);
                    $error = $result['message'] ?? $response->getErrMsg();
                    return ['status' => 1,'msg' => $error];
                }
                break;
            case 'xinke'://鑫科
                $request->setContentTypeFormUrlencoded();
                $response = $request->post($order['orderInfo']);
                $body = $response->getBody();
                $result = unjson($body);
                if(isset($result) && $result['code'] == 200){
                    return ['status' => 0,'payurl' => $result['data']['pay_url']];
                }else{
                    $error = $result['message'] ?? $response->getErrMsg();
                    return ['status' => 1,'msg' => $error];
                }
                break;
            default:
                return ['status' => 1,'msg' => '支付失败'];
                break;
        }
    }





}
