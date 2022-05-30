<?php

namespace App\HttpController\Admin\Fiscal;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Flows;
use App\Model\Orders;
use App\Model\Users;
use Carbon\Carbon;

class FlowController extends AuthController
{


    public function list()
    {
        try {
            $param = $this->params();
            $param['page'] = (int)$param['page'] ?? 1;
            $param['start'] = (string)$param['start'] ?? '';
            $param['end'] = (string)$param['end'] ?? '';
            $param['aid'] = (int)$param['aid'] ?? 0;
            $param['cid'] = (int)$param['cid'] ?? 0;
            $model = Flows::create();
            $lists = $model->list($param);
            $totals = $model->plus($param);
            return $this->writeJson(0, encrypt_data(['lists' => $lists, 'totals' => $totals]));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


    public function count(){
        try {
            $today = Carbon::now()->toDateString();
            $yesterday = Carbon::now()->subDays()->toDateString();

            $flow = Flows::create();
            //今天/昨天安装
            $todayInstall = $flow->install($today);
            $yesterdayInstall = $flow->install($yesterday);

            //今天/昨天活跃用户
            $user = Users::create();
            $todayActive = $user->brisk($today);
            $yesterdayActive = $user->brisk($yesterday);

            $order = Orders::create();
            //新用户支付订单数
            $newUserOrderPayNum = $order->pay($today, 1);
            //老用户支付订单数
            $oldUserOrderPayNum = $order->pay($today, 2);
            //新用户订单金额
            $newUserOrderMoney = $order->money($today, 1);
            //老用户订单金额
            $oldUserOrderMoney = $order->money($today, 2);
            //新用户订单数
            $newUserOrderNum = $order->number($today, 1);
            //老用户订单数
            $oldUserOrderNum = $order->number($today, 2);

            return $this->writeJson(0, encrypt_data([
                'todayInstall' => $todayInstall,
                'yesterdayInstall' => $yesterdayInstall,
                'todayActive' => $todayActive,
                'yesterdayActive' => $yesterdayActive,
                'newUserOrderPayNum' => $newUserOrderPayNum,
                'oldUserOrderPayNum' => $oldUserOrderPayNum,
                'newUserOrderMoney' => $newUserOrderMoney,
                'oldUserOrderMoney' => $oldUserOrderMoney,
                'newUserOrderNum' => $newUserOrderNum,
                'oldUserOrderNum' => $oldUserOrderNum
            ]));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }




}
