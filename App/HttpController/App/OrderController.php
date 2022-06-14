<?php

namespace App\HttpController\App;



use App\Model\Orders;
use App\Model\Users;
use App\Queue\ReportQueue;
use Carbon\Carbon;
use EasySwoole\Queue\Job;
use EasySwoole\RedisPool\RedisPool;
use ZxInc\Zxipdb\IPTool;

class OrderController extends AuthController
{

    public function index()
    {
        try {
            //订单数据
            $data = $this->params();
            //判断支付通道是否可用
            $channel = $this->channel($data['payment']);
            if(!$channel){
                $msg = $data['payment'] == 1 ? '微信通道暂时不可用，请选择支付宝通道下单' : '支付宝通道暂时不可用，请选择微信通道下单';
                return $this->writeJson(1, null, $msg);
            }

            //判断支付金额是否可用,返回支付渠道标识
            $platform = $this->platform($data['type'],$data['payment']);
            if(!$platform){
                return $this->writeJson(1, null, '该金额暂无支付渠道，请选择其他金额下单');
            }
            //配置
            $config = setting();
            //限制
            $limit = $this->limit($this->userid,$config['limit_order_num'],$config['limit_order_time']);
            if($limit){
                return $this->writeJson(1, null, '您的操作太频繁，请稍后再试');
            }

            //订单重复验证
            $model = Orders::create();
            $has = $model->field('id')->where('number', $data['oid'])->get();
            if($has){
                return $this->writeJson(1,null,'请勿重复提交订单');
            }

            //订单基本信息
            $mtype = $this->mtype($data['type']);
            $old = 1;//注册时间超过24小时算老用户充值
            $system = 1;//1安卓 2IOS
            $share = 1;
            $time = Carbon::now();
            $order = [
                'number'    => $data['oid'],
                'title'     => $data['title'],
                'money'     => $data['money'],
                'mtype'     => $mtype,
                'diamond'   => $data['diamond'],
                'platform'  => $platform,
                'payment'   => $data['payment'],
                'status'    => 1,
                'type'      => $data['cate'],
                'system'    => $system,
                'aid'       => 0,
                'cid'       => $data['canalid'],
                'uid'       => 0,
                'username'  => '',
                'settle'    => 1,
                'old'       => $old,
                'share'     => $share,
                'created_at'=> $time,
            ];
            $oid = $model->data($order)->save();
            if($oid){
                $this->queueReport([
                    'info' => [
                        'aid' => 0,
                        'cid' => $data['canalid'],
                        'date' => $time->toDateString(),
                        'hour' => $time->hour,
                    ],
                    'monad' => true,
                ]);
                $appUrl = trim($config['notify_url'],'/');
                return $this->writeJson(0, encrypt_data([
                    'payurl' => $appUrl.'/app/pay?orderid='.$order['number'],
                ]));
            }else{
                return $this->writeJson(1, null, '抱歉，生成订单失败');
            }
        } catch (\Throwable $e) {
            write_log('Order-index:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }

    }


    public function check(){
        try {
            $param = $this->params();
            $oid = $param['oid'] ?? '';
            if($oid){
                $model = Orders::create();
                $info = $model->where('number', $oid)->order('created_at','desc')->get();
                if($info && $info['status'] == 2){
                    return $this->writeJson(0);
                }
            }
            return $this->writeJson(1);
        } catch (\Throwable $e) {
            write_log('Order-check:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }

    //金额类型
    protected function mtype($type){
        switch ($type){
            case 1://月卡
                $date = 'month_vip';
                break;
            case 2://季卡
                $date = 'quarter_vip';
                break;
            case 3://年卡
                $date = 'year_vip';
                break;
            case 4://终身卡
                $date = 'forever_vip';
                break;
            default://日卡
                $date = 'day_vip';
                break;
        }
        return $date;
    }



    /*
     * 金额是否有支付渠道可用
     */
    protected function platform($type,$payment){
        $config = setting();
        switch ($type){
            case 1://月卡
                $date = 'month';
                break;
            case 2://季卡
                $date = 'quarter';
                break;
            case 3://年卡
                $date = 'year';
                break;
            case 4://终身卡
                $date = 'forever';
                break;
            default://日卡
                $date = 'day';
                break;
        }
        $way = $payment == 1 ? '_wechat_' : '_alipay_';
        $platforms = $config['payment_type'.$way.$date];
        $platforms = explode('-',$platforms);
        $key = array_rand($platforms);
        return $platforms[$key];
    }

    /*
    * 通道是否可用
    */
    protected function channel($payment){
        $config = setting();
        if($payment == 1){//微信
            return $config['payment_wechat'] ? true : false;
        }
        if($payment == 2){//支付宝
            return $config['payment_alipay'] ? true : false;
        }
        return false;
    }


    /*
     * 订单限制
     * $num 限制个数
     * $time 限制时间
     * 多少时间内只能提交多少订单，默认0分钟内只允许提交0个订单,即不限制
     */
    protected function limit($uid, $num, $time){
        if(!$num){
            return false;
        }
        $redis = RedisPool::defer();
        $key = 'limit:user_'.$uid;
        $exists = $redis->exists($key);
        if ($exists) {
            $count = $redis->get($key);
            if($count >= $num){
                return true;
            }
            //+1
            $redis->incr($key);
            return false;
        }
        //首次下单
        //将 $key 中储存的数字值+1
        $redis->incr($key);
        // 首次计数 设定过期时间
        $redis->expire($key, $time * 60);
        return false;
    }


}
