<?php

namespace App\Model;


use EasySwoole\Mysqli\QueryBuilder;

class Trades extends Base
{
    protected $tableName = 'trades';

    const STATUS_1 = 1;//未结算
    const STATUS_2 = 2;//已结算

    const STATUS_TEXT = [
        self::STATUS_2 => '<span class="status1">已结算</span>',
        self::STATUS_1 => '<span class="status3">未结算</span>',
    ];

    public function list($param = [], $fields = '*', $limit = 10)
    {
        $lists = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->withTotalCount()
            ->order('id','desc')
            ->order('date','desc')
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['kwd']) && $param['kwd']) {
                    $query->where('username', '%'.$param['kwd'].'%', 'like');
                }
                if (isset($param['start']) && $param['start']) {
                    $query->where('date', $param['start'], '>=');
                }
                if (isset($param['end']) && $param['end']) {
                    $query->where('date', $param['end'], '<=');
                }
                if (isset($param['userid']) && $param['userid']) {
                    $query->where('userid', $param['userid']);
                }
                if (isset($param['status']) && $param['status']) {
                    $query->where('status', $param['status']);
                }
                if (isset($param['role']) && $param['role']) {
                    $query->where('role', $param['role']);
                }
            });
        $lists = $this->paginate($lists, $param['page'], $limit);
        state_to_text($lists['data'], [
            'status' => self::STATUS_TEXT,
        ]);
       return $lists;
    }


}
