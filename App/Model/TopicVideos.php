<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\RedisPool\RedisPool;

class TopicVideos extends Base
{
    protected $tableName = 'topic_videos';


    public function topic()
    {
        return $this->hasOne(Topics::class, function ($builder){
            $builder->fields('id,title');
        }, 'tid', 'id');
    }

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
            ->with(['topic'])
            ->all(function (QueryBuilder $query) use ($param) {
                if(isset($param['kwd']) && $param['kwd']) {
                    $query->where('name LIKE "%'.$param['kwd'].'%"');
                }
                if (isset($param['tid']) && $param['tid']) {
                    $query->where('tid', $param['tid']);
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        return $lists;
    }

    //APP 列表
    public function app($tid = 0)
    {
        return $this->field('id,title,vtime,target,tid')
            ->where('tid', $tid)
            ->all();
    }



}
