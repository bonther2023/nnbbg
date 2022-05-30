<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\RedisPool\RedisPool;

class Articles extends Base
{
    protected $tableName = 'articles';

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

    public function category()
    {
        return $this->hasOne(Categorys::class, function ($builder){
            $builder->fields('id,name');
        }, 'cid', 'id');
    }

    public function list($param = [], $fields = '*', $limit = 8)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('id','desc')
            ->withTotalCount()
            ->with(['category'])
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['kwd']) && $param['kwd']) {
                    $query->where('title','%' . $param['kwd'] . '%','LIKE');
                }
                if (isset($param['cid']) && $param['cid']) {
                    $query->where('cid', $param['cid']);
                }
            });
        return $this->paginate($data, $param['page'], $limit);
    }




    //APP 详情
    public function info($id){
        return $this->field('id,title,html,money,view,created_at')->get($id);
    }

}
