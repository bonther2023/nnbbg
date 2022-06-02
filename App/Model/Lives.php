<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\RedisPool\RedisPool;

class Lives extends Base
{
    protected $tableName = 'lives';

    const STATUS_1 = 1;
    const STATUS_2 = 2;
    const STATUS_3 = 3;

    const STATUS_TEXT = [
        self::STATUS_1 => '<span class="status1">正常</span>',
        self::STATUS_2 => '<span class="status2">推荐</span>',
        self::STATUS_3 => '<span class="status3">锁定</span>',
    ];


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
            ->all(function (QueryBuilder $query) use ($param) {
                if(isset($param['kwd']) && $param['kwd']) {
                    $query->where('title LIKE "%'.$param['kwd'].'%"');
                }
                if (isset($param['status']) && $param['status']) {
                    $query->where('status', $param['status']);
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        state_to_text($lists['data'], [
            'status' => self::STATUS_TEXT,
        ]);
        return $lists;
    }

    public function select(){
        return $this->field('id,title')->all();
    }

    //APP 列表
    public function app()
    {
        return $this->order('RAND()')
            ->limit(20)
            ->all();
    }


}
