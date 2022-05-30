<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\RedisPool\RedisPool;

class Costs extends Base
{
    protected $tableName = 'costs';


    const TYPE_1 = 1;
    const TYPE_2 = 2;

    const TYPE_TEXT = [
        self::TYPE_1 => '<span class="status1">增加</span>',
        self::TYPE_2 => '<span class="status3">减少</span>',
    ];

    public function list($param = [], $fields = '*', $limit = 10)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('id','desc')
            ->withTotalCount()
            ->all(function (QueryBuilder $query) use ($param) {
                if(isset($param['title']) && $param['title']) {
                    $query->where('title LIKE "%'.$param['title'].'%"');
                }
                if(isset($param['uid']) && $param['uid']) {
                    $query->where('uid', $param['uid']);
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        $moneys = $this->field('sum(money) as total')
            ->all(function (QueryBuilder $query) use ($param) {
                if(isset($param['title']) && $param['title']) {
                    $query->where('title LIKE "%'.$param['title'].'%"');
                }
                if(isset($param['uid']) && $param['uid']) {
                    $query->where('uid', $param['uid']);
                }
            });
        $lists['money'] = $moneys[0]['total'];
        state_to_text($lists['data'], [
            'type' => self::TYPE_TEXT,
        ]);
        return $lists;
    }

    //APP 列表
    public function app($param = [],$limit = 20)
    {
        $data = $this->field('id,title,money,type,created_at')
            ->where('uid', $param['uid'])
            ->order('id','desc')
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->withTotalCount()
            ->all();
        return $this->paginate($data, $param['page'], $limit);
    }

    public function add($uid = 0, $money = 0, $title = '', $type = 2){
        $costData = [
            'title' => $title,
            'uid' => $uid,
            'money' => $money,
            'type' => $type,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        return $this->data($costData)->save();
    }

}
