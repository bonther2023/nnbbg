<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\RedisPool\RedisPool;

class Anchors extends Base
{
    protected $tableName = 'anchors';

    const STATUS_1 = 1;
    const STATUS_2 = 2;
    const STATUS_3 = 3;

    const STATUS_TEXT = [
        self::STATUS_1 => '<span class="status1">正常</span>',
        self::STATUS_2 => '<span class="status2">推荐</span>',
        self::STATUS_3 => '<span class="status3">锁定</span>',
    ];

    const ONLINE_1 = 1;
    const ONLINE_2 = 2;

    const ONLINE_TEXT = [
        self::ONLINE_1 => '<span class="status1">离线</span>',
        self::ONLINE_2 => '<span class="status2">在线</span>',
    ];


    public function live()
    {
        return $this->hasOne(Lives::class, function ($builder){
            $builder->fields('id,title');
        }, 'lid', 'id');
    }

    protected function getCreatedAtAttr($value, $data)
    {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }

    public function list($param = [], $fields = '*', $limit = 8)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('id','desc')
            ->withTotalCount()
            ->with(['live'])
            ->all(function (QueryBuilder $query) use ($param) {
                if(isset($param['kwd']) && $param['kwd']) {
                    $query->where('title LIKE "%'.$param['kwd'].'%"');
                }
                if (isset($param['status']) && $param['status']) {
                    $query->where('status', $param['status']);
                }
                if (isset($param['lid']) && $param['lid']) {
                    $query->where('lid', $param['lid']);
                }
                if (isset($param['online']) && $param['online']) {
                    $query->where('online', $param['online']);
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        state_to_text($lists['data'], [
            'status' => self::STATUS_TEXT,
            'online' => self::ONLINE_TEXT,
        ]);
        return $lists;
    }

    public function app($param = [], $limit = 10)
    {
        $data = $this->field('id,title,thumb,lid,status,money,online')
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('online','desc')
            ->order('status','desc')
            ->withTotalCount()
            ->all(function (QueryBuilder $query) use ($param) {
                $query->where('status', self::STATUS_3, '<');
                if (isset($param['lid']) && $param['lid']) {
                    $query->where('lid', $param['lid']);
                }
            });
        return$this->paginate($data, $param['page'], $limit);
    }


    //APP 详情
    public function info($id){
        return $this->field('id,title,thumb,lid,target,money')->where('status', self::STATUS_3, '<')->get($id);
    }



}
