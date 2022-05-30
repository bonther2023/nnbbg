<?php

namespace App\Model;

use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\RedisPool\RedisPool;

class Categorys extends Base
{
    protected $tableName = 'categorys';

    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;
    const TYPE_4 = 4;
    const TYPE_5 = 5;
    const TYPE_6 = 6;
    const TYPE_7 = 7;
    const TYPE_8 = 8;
    const TYPE_9 = 9;

    const TYPE_TEXT = [
        self::TYPE_1 => '<span class="status1">普通</span>',
        self::TYPE_2 => '<span class="status1">媒体</span>',
        self::TYPE_3 => '<span class="status1">楼凤</span>',
        self::TYPE_4 => '<span class="status1">短篇</span>',
        self::TYPE_5 => '<span class="status1">长篇</span>',
        self::TYPE_6 => '<span class="status1">有声</span>',
        self::TYPE_7 => '<span class="status1">漫画</span>',
        self::TYPE_8 => '<span class="status1">套图</span>',
        self::TYPE_9 => '<span class="status1">性闻</span>',
    ];

    //列表
    public function list($param = [], $fields = '*', $limit = 10)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('id', 'desc')
            ->withTotalCount()
            ->all(function (QueryBuilder $query) use ($param) {
                if(isset($param['type']) && $param['type']) {
                    $query->where('type', $param['type']);
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        state_to_text($lists['data'], [
            'type' => self::TYPE_TEXT,
        ]);
        return $lists;
    }

    //类目
    public function select($type = 0,$fields = 'id,name'){
        return $this->field($fields)->all(
            function (QueryBuilder $query) use ($type) {
                if($type) {
                    $query->where('type', $type);
                }
            }
        );
    }

    //APP 类目
    public function app($type = 0){
        $cache = RedisPool::defer();
        $key = 'category:type_'.$type;
        $cate = $cache->get($key);
        if (!$cate) {
            $cate = $this->select($type, 'id,name,icon,type');
            $cache->set($key, $cate, 120);
        }
        return $cate;
    }




}
