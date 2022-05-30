<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;

class NovelChapters extends Base
{
    protected $tableName = 'novel_chapters';

    public function novel()
    {
        return $this->hasOne(Novels::class, function ($builder) {
            $builder->fields('id,thumb,title,money');
        }, 'nid', 'id');
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

    public function list($param = [], $fields = '*', $limit = 10)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('id','desc')
            ->withTotalCount()
            ->all(function (QueryBuilder $query) use ($param) {
                if(isset($param['kwd']) && $param['kwd']) {
                    $query->where('title LIKE "%'.$param['kwd'].'%"');
                }
                if(isset($param['nid']) && $param['nid']) {
                    $query->where('nid', $param['nid']);
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        return $lists;
    }

    //APP 列表
    public function app($rid = 0)
    {
        return $this->field('id,title')
            ->where('nid', $rid)
            ->order('id', 'asc')
            ->all();
    }


    //APP 详情
    public function info($id)
    {
        return $this->field('id,title,nid,html,created_at')->with(['novel'])->get($id);
    }


    //APP 上一章节
    public function prev($rid = 0, $id = 0)
    {
        $data = $this->field('id')
            ->where('nid', $rid)
            ->where('id', $id, '<')
            ->order('id', 'desc')
            ->get();
        return $data ? $data['id'] : 0;
    }


    //APP 下一章节
    public function next($rid = 0, $id = 0)
    {
        $data = $this->field('id')
            ->where('nid', $rid)
            ->where('id', $id, '>')
            ->order('id', 'asc')
            ->get();
        return $data ? $data['id'] : 0;
    }

}
