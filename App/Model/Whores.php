<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\RedisPool\RedisPool;

class Whores extends Base
{
    protected $tableName = 'whores';

    const STATUS_1 = 1;
    const STATUS_2 = 2;
    const STATUS_3 = 3;

    const STATUS_TEXT = [
        self::STATUS_1 => '<span class="status1">正常</span>',
        self::STATUS_2 => '<span class="status2">推荐</span>',
        self::STATUS_3 => '<span class="status3">锁定</span>',
    ];

    const PROJECTS = [
        ['name' => '全部'],
        ['name' => '鸳鸯浴'], ['name' => '莞式全套'], ['name' => '按摩'], ['name' => '推油'], ['name' => 'SPA'],
        ['name' => '洗浴'], ['name' => '足浴'], ['name' => '前列腺保养'], ['name' => '黑丝诱惑'], ['name' => '制服诱惑']
    ];
    const TAGS = [
        ['name' => '全部'],
        ['name' => '空姐'], ['name' => '学生'], ['name' => '护士'], ['name' => '苗条'], ['name' => '老师'], ['name' => '巨乳'],
        ['name' => '少妇'], ['name' => '姐妹花'], ['name' => '母女'], ['name' => '处女'], ['name' => '演员'], ['name' => '鸭子'],
        ['name' => '性感'], ['name' => '萝莉'], ['name' => '嫩模'], ['name' => '混血'], ['name' => '洋妞'], ['name' => '网红'],
        ['name' => 'OL']
    ];
    const CUPS = [
        ['name' => '全部'],
        ['name' => 'A'], ['name' => 'B'], ['name' => 'C'], ['name' => 'D'],
        ['name' => 'E'], ['name' => 'F'], ['name' => 'F+'], ['name' => 'G'], ['name' => 'G+']
    ];

    public function category()
    {
        return $this->hasOne(Categorys::class, function ($builder) {
            $builder->fields('id,name');
        }, 'cid', 'id');
    }

    protected function getImgsAttr($value, $data)
    {
        return unserialize(preg_replace_callback('#s:(\d+):"(.*?)";#s',function($match){return 's:'.strlen($match[2]).':"'.$match[2].'";';},$value));
    }

    protected function setImgsAttr($value, $data)
    {
        return serialize($value);
    }

    protected function getTagsAttr($value, $data)
    {
        return explode('、', $value);
    }

    protected function getProjectAttr($value, $data)
    {
        return explode('、', $value);
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
            ->with(['category'])
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['kwd']) && $param['kwd']) {
                    $query->where('title','%' . $param['kwd'] . '%','LIKE');
                }
                if (isset($param['cid']) && $param['cid']) {
                    $query->where('cid', $param['cid']);
                }
                if (isset($param['status']) && $param['status']) {
                    $query->where('status', $param['status']);
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        state_to_text($lists['data'], [
            'status' => self::STATUS_TEXT,
        ]);
        return $lists;
    }

    //APP 列表
    public function app($param = [], $limit = 20)
    {
        $data = $this->field('id,title,thumb,focus,sale,city,age,money,price,height')
            ->where('status', self::STATUS_3, '<')
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('id', 'desc')
            ->withTotalCount()
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['project']) && $param['project'] && $param['project'] != '全部') {
                    $query->where('title', '%' . $param['project'] . '%', 'LIKE');
                }
                if (isset($param['tag']) && $param['tag'] && $param['tag'] != '全部') {
                    $query->where('tags', '%' . $param['tag'] . '%', 'LIKE');
                }
                if (isset($param['cup']) && $param['cup'] && $param['cup'] != '全部') {
                    $query->where('cup', $param['cup']);
                }
                if (isset($param['cid']) && $param['cid']) {
                    $query->where('cid', $param['cid']);
                }
                if (isset($param['city']) && $param['city'] && $param['city'] != '全国') {
                    $query->where('city', $param['city']);
                }
                if (isset($param['kwd']) && $param['kwd']) {
                    $query->where('title','%' . $param['kwd'] . '%','LIKE');
                }
            });
        return $this->paginate($data, $param['page'], $limit);
    }

    //APP 推荐
    public function good()
    {
        return $this->field('id,title,thumb,focus,sale,city,age,money,price,height')
            ->where('status', self::STATUS_2)
            ->order('RAND()')
            ->limit(20)
            ->all();
    }

    //APP 推荐
    public function latest()
    {
        return $this->field('id,title,thumb,focus,sale,city,age,money,price,height')
            ->where('status', self::STATUS_3, '<')
            ->order('id', 'DESC')
            ->limit(20)
            ->all();
    }

    //APP 详情
    public function info($id, $fields = '*')
    {
        return $this->field('id,title,thumb,imgs,view,focus,city,address,contact,num,age,money,price,project,btime,tags,height,method,cup,other,status,outside,techno,ambient,tariff')
            ->where('status', self::STATUS_3, '<')
            ->get($id);
    }


}
