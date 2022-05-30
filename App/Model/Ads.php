<?php

namespace App\Model;


use Carbon\Carbon;
use EasySwoole\RedisPool\RedisPool;

class Ads extends Base
{
    protected $tableName = 'ads';

    protected function getThumbAttr($value, $data)
    {
        return unserialize($value);
    }

    protected function setThumbAttr($value, $data)
    {
        return serialize($value);
    }

    protected function getCreatedAtAttr($value, $data)
    {
        return Carbon::parse($value)->format('Y-m-d H:i');
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

    //APP 广告
    public function app($name = ''){
        $cache = RedisPool::defer();
        $key = 'ad:'.$name;
        $ad = $cache->get($key);
        if (!$ad) {
            $ad = $this->field('id,thumb,width,height')
                ->where('position',$name)
                ->get();
            $cache->set($key, $ad, 120);
        }
        return $ad;
    }


}
