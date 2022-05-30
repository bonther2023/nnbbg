<?php

namespace App\Model;

use EasySwoole\Mysqli\QueryBuilder;

class Pays extends Base
{
    protected $tableName = 'pays';

    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;
    const TYPE_TEXT = [
        self::TYPE_1 => '<span>微信宝</span>',
        self::TYPE_2 => '<span>支付宝</span>',
        self::TYPE_3 => '<span>双端</span>',
    ];

    const STATUS_1 = 1;
    const STATUS_2 = 2;

    const STATUS_TEXT = [
        self::STATUS_1 => '<span class="status1">正常</span>',
        self::STATUS_2 => '<span class="status3">锁定</span>',
    ];

    const PAY_METHOD_1 = 1;
    const PAY_METHOD_2 = 2;
    const PAY_METHOD_3 = 3;
    const PAY_METHOD_TEXT = [
        self::PAY_METHOD_1 => '<span>POST</span>',
        self::PAY_METHOD_2 => '<span>GET</span>',
        self::PAY_METHOD_3 => '<span>PJSON</span>',
    ];


    const PAY_FORMAT_1 = 1;
    const PAY_FORMAT_2 = 2;
    const PAY_FORMAT_3 = 3;
    const PAY_FORMAT_TEXT = [
        self::PAY_FORMAT_1 => '<span>JSON</span>',
        self::PAY_FORMAT_2 => '<span>FORM</span>',
        self::PAY_FORMAT_3 => '<span>CURL</span>',
    ];


    public function list($param = [], $fields = '*', $limit = 10)
    {
        $data = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->withTotalCount()
            ->order('status','asc')
            ->order('id','desc')
            ->all(function (QueryBuilder $query) use ($param) {
                if(isset($param['kwd']) && $param['kwd']) {
                    $query->where('title LIKE "%'.$param['kwd'].'%"');
                }
                if(isset($param['status']) && $param['status']) {
                    $query->where('status', $param['status']);
                }
            });
        $lists = $this->paginate($data, $param['page'], $limit);
        state_to_text($lists['data'], [
            'type' => self::TYPE_TEXT,
            'status' => self::STATUS_TEXT,
            'pay_format' => self::PAY_FORMAT_TEXT,
            'pay_method' => self::PAY_METHOD_TEXT,
        ]);
        return $lists;
    }

    public function select(){
        $data = $this->where('status',Pays::STATUS_1)
            ->field('title,name,type')
            ->order('id','desc')
            ->all();
        return $data;
    }


    public function info($name){
        $info = $this->where('name',$name)->get();
        switch ($info['pay_method']){
            case 2:
                $info['pay_method'] = 'GET';
                break;
            case 3:
                $info['pay_method'] = 'PJSON';
                break;
            default:
                $info['pay_method'] = 'POST';
                break;
        }
        switch ($info['pay_format']){
            case 2:
                $info['pay_format'] = 'FORM';
                break;
            case 3:
                $info['pay_format'] = 'CURL';
                break;
            default:
                $info['pay_format'] = 'JSON';
                break;
        }
        return $info;
    }

}
