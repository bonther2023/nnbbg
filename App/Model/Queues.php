<?php

namespace App\Model;


use Carbon\Carbon;
use EasySwoole\RedisPool\RedisPool;

class Queues extends Base
{
    protected $tableName = 'queues';

    protected function getDataAttr($value, $data)
    {
        return unserialize($value);
    }

    protected function setDataAttr($value, $data)
    {
        return serialize($value);
    }


    public function list($param = [], $fields = '*', $limit = 10)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('id','desc')
            ->withTotalCount()
            ->all();
        $lists = $this->paginate($data, $param['page'], $limit);
        return $lists;
    }

    public function add($name= '', $data = [], $message = ''){
        $insert = [
            'name' => $name,
            'data' => $data,
            'message' => $message,
            'created_at' => Carbon::now(),
        ];
        return $this->data($insert)->save();
    }


}
