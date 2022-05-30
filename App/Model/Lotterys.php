<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\RedisPool\RedisPool;

class Lotterys extends Base
{
    protected $tableName = 'lotterys';

    protected function getCreatedAtAttr($value, $data)
    {
        return  Carbon::parse($value)->format('Y-m-d H:i');
    }

    //APP 列表
    public function app()
    {
        return $this->field('id,title,created_at')
            ->order('id','desc')
            ->limit(20)
            ->all();
    }


    public function add($data = []){
        return $this->data($data)->save();
    }

}
