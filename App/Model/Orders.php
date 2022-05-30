<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;

class Orders extends Base
{
    protected $tableName = 'orders';

    const STATUS_1 = 1;//未支付
    const STATUS_2 = 2;//已支付

    const STATUS_TEXT = [
        self::STATUS_2 => '<span class="status1">已付</span>',
        self::STATUS_1 => '<span class="status3">未付</span>',
    ];

    const PAYMENT_1 = 1;//微信
    const PAYMENT_2 = 2;//支付宝

    const SYSTEM_1 = 1;//安卓
    const SYSTEM_2 = 2;//IOS

    const SETTLE_1 = 1;//结算
    const SETTLE_2 = 2;//扣量

    const OLD_1 = 1;//新
    const OLD_2 = 2;//老

    const SHARE_1 = 1;//否
    const SHARE_2 = 2;//是

    const TYPE_1 = 1;//会员
    const TYPE_2 = 2;//钻石




    public function list($param = [], $fields = '*', $limit = 9)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('id','desc')
            ->withTotalCount()
            ->all(function (QueryBuilder $query) use ($param){
                if (isset($param['payment']) && $param['payment']) {
                    $query->where('payment', $param['payment']);
                }
                if (isset($param['platform']) && $param['platform']) {
                    $query->where('platform', $param['platform']);
                }
                if (isset($param['system']) && $param['system']) {
                    $query->where('system', $param['system']);
                }
                if (isset($param['status']) && $param['status']) {
                    $query->where('status', $param['status']);
                }
                if (isset($param['share']) && $param['share']) {
                    $query->where('share', $param['share']);
                }
                if (isset($param['cid']) && $param['cid']) {
                    $query->where('cid', $param['cid']);
                }
                if (isset($param['type']) && $param['type']) {
                    $query->where('type', $param['type']);
                }
                if(isset($param['kwd']) && $param['kwd']) {
                    $query->where('(`number` = "'.$param['kwd'].'" OR `uid` = "'.$param['kwd'].'" OR `username` LIKE "%'.$param['kwd'].'%")');
                }
                if (isset($param['start']) && $param['start']) {
                    $query->where('created_at', $param['start'].' 00:00:00', '>=');
                }
                if (isset($param['end']) && $param['end']) {
                    $query->where('created_at', $param['end'].' 23:59:59', '<=');
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        state_to_text($lists['data'], [
            'status' => self::STATUS_TEXT,
        ]);
        $total = $this->brisk($param);
        return ['lists' => $lists,'total' => $total];
    }

    protected function brisk($param = []){
        $moneys = $this->field('sum(money) as total')
            ->all(function (QueryBuilder $query) use ($param){
                if (isset($param['payment']) && $param['payment']) {
                    $query->where('payment', $param['payment']);
                }
                if (isset($param['platform']) && $param['platform']) {
                    $query->where('platform', $param['platform']);
                }
                if (isset($param['system']) && $param['system']) {
                    $query->where('system', $param['system']);
                }
                if (isset($param['status']) && $param['status']) {
                    $query->where('status', $param['status']);
                }
                if (isset($param['share']) && $param['share']) {
                    $query->where('share', $param['share']);
                }
                if (isset($param['type']) && $param['type']) {
                    $query->where('type', $param['type']);
                }
                if (isset($param['cid']) && $param['cid']) {
                    $query->where('cid', $param['cid']);
                }
                if(isset($param['kwd']) && $param['kwd']) {
                    $query->where('(`number` = "'.$param['kwd'].'" OR `uid` = "'.$param['kwd'].'" OR `username` LIKE "%'.$param['kwd'].'%")');
                }
                if(isset($param['name']) && $param['name']) {
                    $query->where('username',  '%'.$param['name'].'%','LIKE');
                }
                if (isset($param['start']) && $param['start']) {
                    $query->where('created_at', $param['start'].' 00:00:00', '>=');
                }
                if (isset($param['end']) && $param['end']) {
                    $query->where('created_at', $param['end'].' 23:59:59', '<=');
                }
            });
        return $moneys[0]['total'];
    }

    //统计订单数
    public function number($date = '', $old = 1){
        $data = $this->where('created_at', $date.' 00:00:00', '>=')
                ->where('created_at', $date.' 23:59:59', '<=')
                ->where('old', $old)
                ->count();
        return $data;
    }

    //统计订单支付数
    public function pay($date = '', $old = 1){
        $data = $this->where('created_at', $date.' 00:00:00', '>=')
            ->where('created_at', $date.' 23:59:59', '<=')
            ->where('status',self::STATUS_2)
            ->where('old', $old)
            ->count();
        return $data;
    }

    //统计订单价格
    public function money($date = '', $old = 1){
        $data = $this->where('created_at', $date.' 00:00:00', '>=')
                ->where('created_at', $date.' 23:59:59', '<=')
                ->where('status',self::STATUS_2)
                ->where('old', $old)
                ->sum('money');
        return $data ?: 0;
    }

//    public function info($id = 0, $fields = '*'){
//        return $this->field($fields)->get();
//
//    }

}
