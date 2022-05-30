<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\RedisPool\RedisPool;

class Buys extends Base
{
    protected $tableName = 'buys';


    //APP 列表
    public function app($param = [],$limit = 10)
    {
        $data = $this->field('id,title,thumb,rid,type')
            ->where('uid', $param['uid'])
            ->where('type', $param['type'])
            ->order('id','desc')
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->withTotalCount()
            ->all();
        return $this->paginate($data, $param['page'], $limit);
    }

    /**
     * 是否购买资源
     * @param int $uid 用户ID
     * @param string $type 资源类别 qovd快播whore约啪sound有声novel长篇comic动漫live直播article性闻topic专题
     * @param int $rid 资源ID
     * @return bool
     */
    public function buy($uid = 0, $type = '', $rid = 0){
        $info = $this->where('uid', $uid)
            ->where('type',$type)
            ->where('rid',$rid)
            ->get();
        return $info ? true : false;
    }

    public function add($data = []){
        return $this->data($data)->save();
    }

}
