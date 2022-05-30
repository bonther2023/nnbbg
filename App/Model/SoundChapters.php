<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;

class SoundChapters extends Base
{
    protected $tableName = 'sound_chapters';

    public function sound()
    {
        return $this->hasOne(Sounds::class, function ($builder) {
            $builder->fields('id,thumb,title,money');
        }, 'sid', 'id');
    }

    protected function getCreatedAtAttr($value, $data)
    {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }

    public function list($param = [], $fields = '*', $limit = 10)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('id', 'desc')
            ->withTotalCount()
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['kwd']) && $param['kwd']) {
                    $query->where('title LIKE "%' . $param['kwd'] . '%"');
                }
                if (isset($param['sid']) && $param['sid']) {
                    $query->where('sid', $param['sid']);
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        return $lists;
    }


    //APP 列表
    public function app($rid = 0)
    {
        return $this->field('id,title')
            ->where('sid', $rid)
            ->order('id', 'asc')
            ->all();
    }


    //APP 详情
    public function info($id)
    {
        return $this->field('id,title,sid,target')->with(['sound'])->get($id);
    }


    //APP 上一章节
    public function prev($rid = 0, $id = 0)
    {
        $data = $this->field('id')
            ->where('sid', $rid)
            ->where('id', $id, '<')
            ->order('id', 'desc')
            ->get();
        return $data ? $data['id'] : 0;
    }


    //APP 下一章节
    public function next($rid = 0, $id = 0)
    {
        $data = $this->field('id')
            ->where('sid', $rid)
            ->where('id', $id, '>')
            ->order('id', 'asc')
            ->get();
        return $data ? $data['id'] : 0;
    }

}
