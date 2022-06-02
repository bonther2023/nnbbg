<?php

namespace App\Model;

use App\Utility\OrderQueue;
use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\Queue\Job;
use EasySwoole\RedisPool\RedisPool;

class Videos extends Base
{
    protected $tableName = 'videos';

    const STATUS_1 = 1;
    const STATUS_2 = 2;
    const STATUS_3 = 3;

    const STATUS_TEXT = [
        self::STATUS_1 => '<span class="status1">正常</span>',
        self::STATUS_2 => '<span class="status2">推荐</span>',
        self::STATUS_3 => '<span class="status3">锁定</span>',
    ];

    const FREE_1 = 1;//正常
    const FREE_2 = 2;//限免

    public function category()
    {
        return $this->hasOne(Categorys::class, function ($builder) {
            $builder->fields('id,name');
        }, 'cid', 'id');
    }

    protected function getTagsAttr($value, $data)
    {
        return explode('、',$value);
    }

    protected function getCreatedAtAttr($value, $data)
    {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }

    public function list($param = [], $fields = '*', $limit = 8)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('id', 'DESC')
            ->withTotalCount()
            ->with(['category'])
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['kwd']) && $param['kwd']) {
                    $query->where('title','%' . $param['kwd'] . '%','LIKE');
                }
                if (isset($param['status']) && $param['status']) {
                    $query->where('status', $param['status']);
                }
                if (isset($param['cid']) && $param['cid']) {
                    $query->where('cid', $param['cid']);
                }
                if (isset($param['free']) && $param['free']) {
                    $query->where('free', $param['free']);
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        state_to_text($lists['data'], [
            'status' => self::STATUS_TEXT,
        ]);
        return $lists;
    }

    //APP 列表
    public function app($param = [],$limit = 20)
    {
        $data = $this->field('id,title,thumb,free,clarity,vtime,view,focus')
            ->where('status', self::STATUS_3, '<')
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('free','DESC')
            ->order('id','DESC')
            ->withTotalCount()
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

    //APP 推荐
    public function good($limit = 10)
    {
        return $this->field('id,title,thumb,clarity,vtime,view,focus')
            ->order('RAND()')
            ->limit($limit)
            ->all();
    }

    //APP 详情
    public function info($id){
        return $this->field('id,title,thumb,target,free,clarity,tags,vtime,view,focus') ->where('status', self::STATUS_3, '<')->get($id);
    }

    //APP 随机
    public function love($limit = 10)
    {
        return $this->field('id,title,thumb,free,clarity,vtime,view,focus')
            ->where('status', self::STATUS_3, '<')
            ->order('RAND()')
            ->limit($limit)
            ->all();
    }



}
