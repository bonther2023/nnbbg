<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\RedisPool\RedisPool;

class Qovds extends Base
{
    protected $tableName = 'qovds';

    const STATUS_1 = 1;
    const STATUS_2 = 2;
    const STATUS_3 = 3;

    const STATUS_TEXT = [
        self::STATUS_1 => '<span class="status1">正常</span>',
        self::STATUS_2 => '<span class="status2">推荐</span>',
        self::STATUS_3 => '<span class="status3">锁定</span>',
    ];


    public function author()
    {
        return $this->hasOne(Authors::class, function ($builder){
            $builder->fields('id,name,avatar');
        }, 'aid', 'id');
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
            ->order('id','desc')
            ->withTotalCount()
            ->with(['author'])
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['kwd']) && $param['kwd']) {
                    $query->where('title','%' . $param['kwd'] . '%','LIKE');
                }
                if (isset($param['status']) && $param['status']) {
                    $query->where('status', $param['status']);
                }
                if (isset($param['aid']) && $param['aid']) {
                    $query->where('aid', $param['aid']);
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
        $data = $this->field('id,title,thumb,aid,money,view,focus')
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->withTotalCount()
            ->with(['author'])
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['status']) && $param['status']) {
                    $query->where('status', $param['status']);
                }else{
                    $query->where('status', self::STATUS_3, '<');
                }
                if (isset($param['kwd']) && $param['kwd']) {
                    $query->where('title','%' . $param['kwd'] . '%','LIKE');
                }
                if (isset($param['aid']) && $param['aid']) {
                    $query->where('aid', $param['aid']);
                }
                if (isset($param['aids']) && $param['aids']) {
                    $query->where('aid', $param['aids'], 'IN');
                }
                if (isset($param['hot']) && $param['hot']) {
                    $query->orderBy('focus');
                }
                if (isset($param['hot']) && $param['hot']) {
                    $query->orderBy('focus');
                }else{
                    $query->orderBy('id');
                }
            });
        return $this->paginate($data, $param['page'], $limit);
    }

    //APP 推荐
    public function good($limit = 10)
    {
        return $this->field('id,title,thumb,aid,money,vtime,view,focus')
            ->where('status', self::STATUS_2)
            ->with(['author'])
            ->order('RAND()')
            ->limit($limit)
            ->all();
    }

    //APP 详情
    public function info($id){
        return $this->field('id,title,thumb,aid,target,money,tags,vtime,focus')
            ->where('status', self::STATUS_3, '<')
            ->with(['author'])
            ->get($id);
    }



}
