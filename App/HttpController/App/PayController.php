<?php

namespace App\HttpController\App;

use App\Model\Orders;
use App\Model\Pays;
use App\Utility\Pay;

class PayController extends AuthController
{

    public function test(){
        $request = $this->request();
        $payment = $request->getRequestParam('payment') ?? 1;
        $money = $request->getRequestParam('money') ?? 30;
        $platform = $request->getRequestParam('platform') ?? '';
        $format = $request->getRequestParam('format') ?? '';
        $config = Pays::create()->info($platform);
        $payOrder = [
            "payment" => $payment, // 支付类型
            "orderid" => $this->build(), // 商户订单号
            "amount"  => $money, // 支付金额，单位元
            "notify"  => trim(setting('notify_url'),'/').'/app/notify/'.$config['return_notify'], // 异步回调，支付结果以异步为准
            "return"  => 'https://www.xxxxxx.com', // 同步回调，不作为最终支付结果为准，请以异步回调为准
            "subject" => '用户支付', // 商品名称
            "ip" => $this->getIp(), // IP
        ];
        $pay = new Pay();
        $func = $platform;
        $payOrderInfo = $pay->order($payOrder,$func,$config);
        // 两种请求支付方式
        if($format == 2){
            //表单形式，form提交请求
            $html = $this->getFormHtml($payOrderInfo);
        }
        if($format == 3){
            //CURL形式
            $curlPayInfo = $pay->send($payOrderInfo,$func);
            if($curlPayInfo['status']){
                return $this->writeEcho($curlPayInfo['msg']);
            }
            if(isset($curlPayInfo['payurl'])){
                $html = $this->getCurlHtml($curlPayInfo['payurl']);
            }else{
                $html = $curlPayInfo['html'];
            }
        }
        return $this->response()->write($html);
    }




    public function index(){
        try {
            $request = $this->request();
            $oid = $request->getRequestParam('orderid') ?? '';

            //查看订单信息
            $model = Orders::create();
            $info = $model->where('number', $oid)->order('created_at','desc')->get();

            if(!$info) return $this->writeEcho('抱歉，订单信息不存在');

            $appUrl = trim(setting('notify_url'),'/');
            $config = Pays::create()->info($info['platform']);
            $order = [
                "payment" => $info['payment'], // 支付类型
                "orderid" => $info['number'], // 商户订单号
                "amount"  => $info['money'], // 支付金额，单位元
                "notify"  => $appUrl.'/app/notify/'.$config['return_notify'], // 异步回调，支付结果以异步为准
                "return"  => 'https://www.xxxxxx.com', // 同步回调，不作为最终支付结果为准，请以异步回调为准
                "subject" => '用户支付', // 商品名称
                "ip" => $this->getIp(),
            ];
            $pay = new Pay();
            $payOrderInfo = $pay->order($order,$info['platform'],$config);
            // 两种请求支付方式
            if($payOrderInfo['orderFormat'] == 'FORM'){
                //表单形式，form提交请求
                $html = $this->getFormHtml($payOrderInfo);
            }
            if($payOrderInfo['orderFormat'] == 'JSON'){
                //json形式，js提交请求
                $html = $this->getJsonHtml($payOrderInfo,$info['platform']);
            }
            if($payOrderInfo['orderFormat'] == 'CURL'){
                //CURL形式
                $curlPayInfo = $pay->send($payOrderInfo,$info['platform']);
                if($curlPayInfo['status']){
                    return $this->writeEcho($curlPayInfo['msg']);
                }
                if(isset($curlPayInfo['payurl'])){
                    $html = $this->getCurlHtml($curlPayInfo['payurl']);
                }else{
                    $html = $curlPayInfo['html'];
                }
            }
            return $this->response()->write($html);
        } catch (\Throwable $e) {
            write_log('Pay-index:');
            write_log($e->getMessage(), 4);
            return $this->response()->write('支付失败');
        }
    }

    protected function getJsonHtml($payOrderInfo,$func){
        $type = "application/x-www-form-urlencoded";
        switch ($func){
            case 'qinaguia'://钱柜支付宝
                $responseCode = 'if(result && result.code == 10000){window.location.href = plus.runtime.openURL(result.cont.url);}else{that.viewTotast(result.msg)}';
                break;
            case 'xiaoding'://小鼎支付
                $responseCode = 'if(result && result.retCode == "SUCCESS"){window.location.href = result.payParams.codeUrl;}else{that.viewTotast(result.retMsg)}';
                break;
            case 'yangyuw': //洋芋话费微信支付
            case 'yangyua': //洋芋话费支付宝支付
                $responseCode = 'if(result && result.status == "0"){window.location.href = plus.runtime.openURL(result.payurl);}else{that.viewTotast(result.pay_msg)}';
                break;
            default:
                $responseCode = 'console.log(456);console.log(JSON.stringify(result));';
                break;
        }
        $head = $this->getHtmlHead();
        $loading = $this->getHtmlLoading();
        //表单html
        return '<!DOCTYPE html>
        <html lang="zh-CN">
            '.$head.'
            <body>
                <div class="container">'.$loading.'<div id="app"></div></div>
                 <script src="https://lib.baomitu.com/vue/2.6.11/vue.min.js"></script>
                <script src="https://lib.baomitu.com/mui/3.7.1/js/mui.min.js"></script>
                <script type="text/javascript">
                    mui.init();
                    mui.plusReady(function(){
                        new Vue({
                            el: "#app",
                            data: function(){},
                            created(){
                                let that = this;
                                setTimeout(function () {
                                     that.formSubmit();
                                }, 1000);
                            },
                            methods:{
                                formSubmit() {
                                    console.log(JSON.stringify('.json_encode($payOrderInfo['orderInfo']).'))
                                    var that = this;
                                    mui.ajax({
                                        type: "'.$payOrderInfo['orderMethod'].'",
                                        url: "'.$payOrderInfo['orderPayUrl'].'",
                                        ContentType: "'.$type.'",
                                        data: '.json_encode($payOrderInfo['orderInfo']).',
                                        dataType: "json",
                                        crossDomain: true,
                                        success:(result)=>{console.log("请求成功");console.log(JSON.stringify(result));'.$responseCode.'},
                                        error: (e)=>{console.log("请求失败");console.log(JSON.stringify(e));that.viewTotast(e.statusText)}
                                    });
                                },
                                viewTotast(msg){
                                    plus.nativeUI.showWaiting(msg);
                                    setTimeout(function () {
                                        plus.nativeUI.closeWaiting();
                                    }, 3000);
                                }
                            }
                        });
                    });
                </script>
            </body>
        </html>';
    }


    protected function getFormHtml($payOrderInfo){
        $head = $this->getHtmlHead();
        $loading = $this->getHtmlLoading();
        //表单隐藏字段
        $formInput = '';
        foreach ($payOrderInfo['orderInfo'] as $key => $val) {
            $formInput .= '<input type="hidden" name="' . $key . '" value="' . $val . '">';
        }
        //表单html
        return '<!DOCTYPE html>
        <html lang="zh-CN">
            '.$head.'
            <body>
                <div class="container">'.$loading.'<form id="payform" method="'.$payOrderInfo['orderMethod'].'" action="'.$payOrderInfo['orderPayUrl'].'">'.$formInput.'</form></div>
                <script type="text/javascript">
                    setTimeout(function () {
                            document.forms["payform"].submit();
                        },1000);
                </script>
            </body>
        </html>';
    }

    public function getCurlHtml($link){
        $head = $this->getHtmlHead();
        $loading = $this->getHtmlLoading();
        return '<!DOCTYPE html>
        <html lang="zh-CN">
            '.$head.'
            <body>
                <div class="container">'.$loading.'</div>
                <script type="text/javascript">
                    setTimeout(function () {
                        if(window.plus){
                             plus.runtime.openURL("'.$link.'")
                        }else{
                            window.location.href = "'.$link.'"
                        }
                    },500);
                </script>
            </body>
        </html>';
    }

    protected function getHtmlLoading(){
        return '<div class="loader-loading">
                <div class="k-ring-1"></div>
                <div class="loader-title">订单支付中...请稍后</div>
                <div class="loader" style="">
                    <div class="loader-inner ball-beat"><div></div><div></div><div></div><div></div><div></div><div></div></div>
                </div>
                <br/>
            </div><div style="font-size: 16px;padding-left: 5%;color: red">支付过程中可能出现短暂白屏现象，请多等几秒...</div><br/>
            <div style="font-size: 20px;text-align:center;color: red;font-weight: 700">支付成功未到账，请联系在线客服</div>
            <br/><div style="font-size: 30px;text-align:center;color: red;font-weight: 700">请勿超时支付</div>';
    }

    protected function getHtmlHead(){
        return '<head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1">
            <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
            <title>订单确认</title>
            <style type="text/css">
                body{
                    margin: 0;width: 100%;height:100%;overflow: hidden;
                }
                .loader-loading{
                    font-size: 22px;margin-top: 200px;padding-left: 15%;
                }
                .loader-title{
                    float: left;
                }
                .loader{
                    float: left;position: relative;top:3px;margin-left: 5px;
                }
                .k-ring-1 {
                    width:10px;
                    height:10px;
                    margin-right: 8px;
                    padding:5px;
                    border:5px dashed #000;float: left;
                    border-radius:100%;
                }
                .k-ring-1 {
                    animation:k-loadingD 1.5s .3s cubic-bezier(.17,.37,.43,.67) infinite
                }
                @keyframes k-loadingD {
                    0 {
                        transform:rotate(0deg)
                    }
                    50% {
                        transform:rotate(180deg)
                    }
                    100% {
                        transform:rotate(360deg)
                    }
                    }@keyframes k-loadingE {
                        0 {
                        transform:rotate(0deg)
                    }
                    100% {
                        transform:rotate(360deg)
                    }
                }

                @-webkit-keyframes ball-beat {
                    15% {
                        opacity: 0.15;
                        -webkit-transform: scale(0.15);
                                transform: scale(0.15);
                    }
                    30% {
                        opacity: 0.3;
                        -webkit-transform: scale(0.30);
                                transform: scale(0.30);
                    }
                    50% {
                        opacity: 0.5;
                        -webkit-transform: scale(0.50);
                                transform: scale(0.50);
                    }
                    65% {
                        opacity: 0.65;
                        -webkit-transform: scale(0.65);
                                transform: scale(0.65);
                    }
                    80% {
                        opacity: 0.8;
                        -webkit-transform: scale(0.80);
                                transform: scale(0.80);
                    }
                    100% {
                        opacity: 1;
                        -webkit-transform: scale(1);
                            transform: scale(1);
                    }
                }

                @keyframes ball-beat {
                    15% {
                        opacity: 0.15;
                        -webkit-transform: scale(0.15);
                                transform: scale(0.15);
                    }
                    30% {
                        opacity: 0.3;
                        -webkit-transform: scale(0.30);
                                transform: scale(0.30);
                    }
                    50% {
                        opacity: 0.5;
                        -webkit-transform: scale(0.50);
                                transform: scale(0.50);
                    }
                    65% {
                        opacity: 0.65;
                        -webkit-transform: scale(0.65);
                                transform: scale(0.65);
                    }
                    80% {
                        opacity: 0.8;
                        -webkit-transform: scale(0.80);
                                transform: scale(0.80);
                    }
                    100% {
                        opacity: 1;
                        -webkit-transform: scale(1);
                            transform: scale(1);
                    }
                }

                .ball-beat > div {
                    background-color: #000;
                    width: 4px;
                    height: 4px;
                    border-radius: 100%;
                    margin: 1px;
                    -webkit-animation-fill-mode: both;
                    animation-fill-mode: both;
                    display: inline-block;
                    -webkit-animation: ball-beat 1.5s 0s infinite linear;
                    animation: ball-beat 1.5s 0s infinite linear;
                 }
                .ball-beat > div:nth-child(2n-1) {
                    -webkit-animation-delay: 0.5s !important;
                    animation-delay: 0.5s !important;
                 }
            </style>
        </head>';
    }

}
