<?php

namespace App\Model;

use EasySwoole\Mysqli\QueryBuilder;

class Canals extends Base
{
    protected $tableName = 'canals';

    const STATUS_1 = 1;
    const STATUS_2 = 2;

    const STATUS_TEXT = [
        self::STATUS_1 => '<span class="status1">正常</span>',
        self::STATUS_2 => '<span class="status3">锁定</span>',
    ];

    public function agent()
    {
        return $this->hasOne(Agents::class, function ($builder){
            $builder->fields('id,username');
        }, 'aid', 'id');
    }

    public function list($param = [], $fields = '*', $limit = 10)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('id','desc')
            ->withTotalCount()
            ->with(['agent'])
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['id']) && $param['id']) {
                    $query->where('id', $param['id']);
                }
                if (isset($param['kwd']) && $param['kwd']) {
                    $query->where('username', $param['kwd']);
                }
                if(isset($param['status']) && $param['status']) {
                    $query->where('status', $param['status']);
                }
                if(isset($param['aid']) && $param['aid']) {
                    $query->where('aid', $param['aid']);
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        foreach ($lists['data'] as &$item){
            $item['password'] = '';
        }
        state_to_text($lists['data'], [
            'status' => self::STATUS_TEXT,
        ]);
        return $lists;
    }


    public function select()
    {
        $lists = $this->field('id,username')
            ->where('status',self::STATUS_1)
            ->order('id')
            ->all();
        return $lists;
    }

}
