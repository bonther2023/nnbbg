<?php

namespace App\Model;

use EasySwoole\Mysqli\QueryBuilder;

class Doings extends Base
{
    protected $tableName = 'doings';

    const STATUS_1 = 1;
    const STATUS_2 = 2;

    const STATUS_TEXT = [
        self::STATUS_1 => '<span class="status1">正常</span>',
        self::STATUS_2 => '<span class="status3">锁定</span>',
    ];

    public function list($param = [], $fields = '*', $limit = 8)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('sort','desc')
            ->withTotalCount()
            ->all(function (QueryBuilder $query) use ($param) {
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

    public function app($param = [], $limit = 10)
    {
        $data = $this->field('id,title,thumb,target')
            ->where('status', self::STATUS_1)
            ->order('sort','desc')
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->withTotalCount()
            ->all();
        $lists = $this->paginate($data, $param['page'], $limit);
        return $lists;
    }



}
