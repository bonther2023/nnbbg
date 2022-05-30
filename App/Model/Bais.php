<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;

class Bais extends Base
{
    protected $tableName = 'bais';

    const STATUS_1 = 1;
    const STATUS_2 = 2;

    const STATUS_TEXT = [
        self::STATUS_1 => '<span class="status1">未中奖</span>',
        self::STATUS_2 => '<span class="status3">中奖</span>',
    ];

    public function list($param = [], $fields = '*', $limit = 8)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->withTotalCount()
            ->order('id','desc')
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['status']) && $param['status']) {
                    $query->where('status', $param['status']);
                }
                if (isset($param['date']) && $param['date']) {
                    $query->where('date', $param['date']);
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        state_to_text($lists['data'], [
            'status' => self::STATUS_TEXT,
        ]);
        return $lists;
    }

    public function app($date)
    {
        return $this->field('username')
            ->where('date', $date)
            ->where('status', self::STATUS_2)
            ->order('id','desc')
            ->all();
    }


    public function code($date)
    {
        $code = invite(6);
        //去重
        $info = $this->field('code')->where('date',$date)->where('code',$code)->get();
        if($info){
            return $this->code();
        }
        return $code;
    }




}
