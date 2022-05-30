<?php

namespace App\HttpController\Admin\Main;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Orders;
use App\Model\Pays;
use App\Model\Users;
use EasySwoole\RedisPool\RedisPool;

class IndexController extends AuthController {



    public function monitor(){
        try {
            $aDayPayments = setting('payment_type_alipay_day');
            $aMonthPayments = setting('payment_type_alipay_month');
            $aQuarterPayments = setting('payment_type_alipay_quarter');
            $aYearPayments = setting('payment_type_alipay_year');
            $aForeverPayments = setting('payment_type_alipay_forever');

            $wDayPayments = setting('payment_type_wechat_day');
            $wMonthPayments = setting('payment_type_wechat_month');
            $wQuarterPayments = setting('payment_type_wechat_quarter');
            $wYearPayments = setting('payment_type_wechat_year');
            $wForeverPayments = setting('payment_type_wechat_forever');

            $aDayPayments = $aDayPayments ? explode('-', $aDayPayments) : [];
            $aMonthPayments = $aMonthPayments ? explode('-', $aMonthPayments) : [];
            $aQuarterPayments = $aQuarterPayments ? explode('-', $aQuarterPayments) : [];
            $aYearPayments = $aYearPayments ? explode('-', $aYearPayments) : [];
            $aForeverPayments = $aForeverPayments ? explode('-', $aForeverPayments) : [];

            $wDayPayments = $wDayPayments ? explode('-', $wDayPayments) : [];
            $wMonthPayments = $wMonthPayments ? explode('-', $wMonthPayments) : [];
            $wQuarterPayments = $wQuarterPayments ? explode('-', $wQuarterPayments) : [];
            $wYearPayments = $wYearPayments ? explode('-', $wYearPayments) : [];
            $wForeverPayments = $wForeverPayments ? explode('-', $wForeverPayments) : [];

            $payments = [
                ['payment' => 2, 'money' => setting('day_vip'), 'pay' => $aDayPayments],
                ['payment' => 2, 'money' => setting('month_vip'), 'pay' => $aMonthPayments],
                ['payment' => 2, 'money' => setting('quarter_vip'), 'pay' => $aQuarterPayments],
                ['payment' => 2, 'money' => setting('year_vip'), 'pay' => $aYearPayments],
                ['payment' => 2, 'money' => setting('forever_vip'), 'pay' => $aForeverPayments],
                ['payment' => 1, 'money' => setting('day_vip'), 'pay' => $wDayPayments],
                ['payment' => 1, 'money' => setting('month_vip'), 'pay' => $wMonthPayments],
                ['payment' => 1, 'money' => setting('quarter_vip'), 'pay' => $wQuarterPayments],
                ['payment' => 1, 'money' => setting('year_vip'), 'pay' => $wYearPayments],
                ['payment' => 1, 'money' => setting('forever_vip'), 'pay' => $wForeverPayments],
            ];
            $result = [];
            foreach ($payments as &$item){
                if(count($item['pay'])){
                    $pays = array_unique($item['pay']);
                    foreach ($pays as $pay){
                        $result[$item['money']][] = $this->getPaygod($pay,$item['money'],$item['payment']);
                    }
                }
            }
            $userModel = Users::create();
            $iosNum = $userModel->field('id')->where('app_system', 'iOS')
                ->where('created_at', date('Y-m-d H:i:s',time()-300), '>=')
                ->count();
            $andNum = $userModel->field('id')->where('app_system', 'Android')
                ->where('created_at', date('Y-m-d H:i:s',time()-300), '>=')
                ->count();
            return $this->writeJson(0, encrypt_data([
                'success' => $result, 'ios' => $iosNum, 'android' => $andNum,
                'warning_order' => setting('warning_order'),
                'warning_ios' => setting('warning_ios'),
                'warning_android' => setting('warning_android')
            ]));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }



    public function getPaygod($name,$money,$payment){
        $model    = Orders::create();
        $payModel = Pays::create();
        $nums    = setting('warning_order');
        $payInfo = $payModel->field('title')->where('name', $name)->get();
        $orders  = $model->field('status')
            ->where('money', $money)
            ->where('platform', $name)
            ->where('payment',$payment)
            ->limit($nums)
            ->order('id', 'desc')
            ->all();
        $yesPayOrders   = [];
        $yesPayOrders[] = array_filter($orders, function ($item) {
            return $item['status'] == 2;
        });
        $yesPay = count($yesPayOrders[0]);
        if($payment == 1){
            $b = 'W';
        }else{
            $b = 'A';
        }
        $result = [];
        $result['platform'] = $payInfo['title'].'('.$b.')';
        $result['yespay']   = $yesPay;
        $result['success']  = count($orders) ? (number_format($yesPay / (count($orders)), 2) * 100) . '%' : '0%';
        if($yesPay < 3 && count($orders) == $nums){
            $result['hong']   = 1;
        }else{
            $result['hong']   = 0;
        }
        return $result;
    }



    public function online(){
        try {
            $redis = RedisPool::defer();
            $time = (int)time()+ 100;
            $_time = $time - (int)(setting('online_time') * 60);
            $nums = $redis->zCount('online', $_time, $time);
            return $this->writeJson(0, encrypt_data($nums));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


}
