<?php

namespace App\Model;

use EasySwoole\Mysqli\QueryBuilder;

class Users extends Base
{
    protected $tableName = 'users';

    const STATUS_1 = 1;
    const STATUS_2 = 2;

    const STATUS_TEXT = [
        self::STATUS_1 => '<span class="status1">正常</span>',
        self::STATUS_2 => '<span class="status3">锁定</span>',
    ];

    const VIP_TEXT = [
        'free_vip' => '免费',
        'day_vip' => '日卡',
        'month_vip' => '月卡',
        'quarter_vip' => '季卡',
        'year_vip' => '年卡',
        'forever_vip' => '终身卡',
    ];

    const RANK_TEXT = ['普通','青铜','白银','黄金','铂金','钻石','星耀','王者'];

    public function agent()
    {
        return $this->hasOne(Agents::class, function ($builder){
            $builder->fields('id,username');
        }, 'aid', 'id');
    }

    public function canal()
    {
        return $this->hasOne(Canals::class, function ($builder){
            $builder->fields('id,username');
        }, 'cid', 'id');
    }

    public function list($param = [], $fields = '*', $limit = 10)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('id','desc')
            ->withTotalCount()
            ->all(function (QueryBuilder $query) use ($param) {
                if(isset($param['kwd']) && $param['kwd']) {
                    $query->where('username',$param['kwd']);
                }
                if(isset($param['id']) && $param['id']) {
                    $query->where('id',$param['id']);
                }
                if(isset($param['mobile']) && $param['mobile']) {
                    $query->where('mobile',$param['mobile']);
                }
                if(isset($param['system']) && $param['system']) {
                    $query->where('app_system', $param['system']);
                }
                if(isset($param['cid']) && $param['cid']) {
                    $query->where('cid', $param['cid']);
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        foreach ($lists['data'] as $key => &$row) {
            $row['vip_at'] = $row['vip_at'] ? date('Y-m-d H:i:s',$row['vip_at']): '';
            $row['vip_text'] = self::VIP_TEXT[$row['vip']] ?? '无';
            $row['status_text'] = self::STATUS_TEXT[$row['status']];
            $row['vip_rank_text'] = self::RANK_TEXT[$row['vip_rank']];
        }
        return $lists;
    }

    public function brisk($date){
        $data = $this->where('login_at', $date.' 00:00:00', '>=')
            ->where('login_at', $date.' 23:59:59', '<=')->count();
        return $data;
    }

    public function info($id = 0){
        return $this->get($id);
    }


    public function clearVip($id){
        return $this->update(['vip' => '', 'vip_at' => 0],['id' => $id]);
    }

}
