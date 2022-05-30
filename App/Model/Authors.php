<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;

class Authors extends Base
{
    protected $tableName = 'authors';


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
                if (isset($param['name']) && $param['name']) {
                    $query->where('name', $param['name']);
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        return $lists;
    }


    public function select()
    {
        $lists = $this->field('id,name')->order('id')->all();
        return $lists;
    }


    //APP 列表
    public function app($param = [])
    {
        return $this->field('id,name,avatar')
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['hot']) && $param['hot']) {
                    $query->limit(10)->orderBy('focus');
                }
                if (isset($param['sale']) && $param['sale']) {
                    $query->limit(10)->orderBy('sale');
                }
                if (isset($param['savor']) && $param['savor']) {
                    if(count($param['aids'])){
                        $query->limit(20)->where('id', $param['aids'],'NOT IN')->orderBy('focus');
                    }else{
                        $query->limit(20)->orderBy('focus');
                    }
                }
                if (empty($param)) {
                    $query->orderBy('id');
                }
        });
    }

}
