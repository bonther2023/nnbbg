<?php

namespace App\Model;
use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;

class News extends Base
{
    protected $tableName = 'news';

    public function author()
    {
        return $this->hasOne(Authors::class, function ($builder){
            $builder->fields('id,name,avatar');
        }, 'aid', 'id');
    }

    protected function getContentAttr($value, $data)
    {
        return unserialize($value);
    }

    protected function setContentAttr($value, $data)
    {
        return serialize($value);
    }

    protected function getCreatedAtAttr($value, $data)
    {
        return  Carbon::parse($value)->format('Y-m-d H:i');
    }

    public function list($param = [], $fields = '*', $limit = 8)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->with(['author'])
            ->order('id','desc')
            ->withTotalCount()
            ->all();
        return $this->paginate($data, $param['page'], $limit);
    }

}
