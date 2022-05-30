<?php

namespace App\Model;


use EasySwoole\Mysqli\QueryBuilder;

class Flows extends Base
{
    protected $tableName = 'flows';

    public function agent()
    {
        return $this->hasOne(Agents::class, function ($builder){
            $builder->fields('id,username');
        }, 'aid', 'id');
    }

    public function canal()
    {
        return $this->hasOne(Canals::class, function ($builder){
            $builder->fields('id,username,percent_canal,percent_agent');
        }, 'cid', 'id');
    }


    public function list($param = [], $fields = '*', $limit = 20)
    {
        $lists = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('date')
            ->order('install_and_settle')
            ->withTotalCount()
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['start']) && $param['start']) {
                    $query->where('date', $param['start'], '>=');
                }
                if (isset($param['end']) && $param['end']) {
                    $query->where('date', $param['end'], '<=');
                }
                if (isset($param['cid']) && $param['cid']) {
                    $query->where('cid', $param['cid']);
                }
                if (isset($param['aid']) && $param['aid']) {
                    $query->where('aid', $param['aid']);
                }
            });
           $lists = $this->paginate($lists, $param['page'], $limit);
        foreach ($lists['data'] as &$item){
            $item['rechage'] = number_format($item['settle_agent'] + $item['settle_canal'] + $item['settle_admin'],2);
        }
        return $lists;
    }

    public function user($param = [], $fields = '*', $limit = 20)
    {
        $lists = $this->field($fields)
            ->limit(($param['page'] - 1) * $limit, $limit)
            ->order('date')
            ->order('install_and_settle')
            ->withTotalCount()
            ->with(['canal'])
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['start']) && $param['start']) {
                    $query->where('date', $param['start'], '>=');
                }
                if (isset($param['end']) && $param['end']) {
                    $query->where('date', $param['end'], '<=');
                }
                if (isset($param['cid']) && $param['cid']) {
                    $query->where('cid', $param['cid']);
                }
                if (isset($param['aid']) && $param['aid']) {
                    $query->where('aid', $param['aid']);
                }
            });
        $lists = $this->paginate($lists, $param['page'], $limit);
        return $lists;
    }

    public function plus($param){
        $lists = $this->field(['SUM(`install_ios_settle`) as install_ios_settle',
            'SUM(`install_and_settle`) as install_and_settle',
            'SUM(`install_ios_deduct`) as install_ios_deduct',
            'SUM(`install_and_deduct`) as install_and_deduct',
            'SUM(`order_ios_settle`) as order_ios_settle',
            'SUM(`order_and_settle`) as order_and_settle',
            'SUM(`order_ios_deduct`) as order_ios_deduct',
            'SUM(`order_and_deduct`) as order_and_deduct',
            'SUM(`settle_agent`) as settle_agent',
            'SUM(`settle_canal`) as settle_canal',
            'SUM(`settle_admin`) as settle_admin'])
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['start']) && $param['start']) {
                    $query->where('date', $param['start'], '>=');
                }
                if (isset($param['end']) && $param['end']) {
                    $query->where('date', $param['end'], '<=');
                }
                if (isset($param['cid']) && $param['cid']) {
                    $query->where('cid', $param['cid']);
                }
                if (isset($param['aid']) && $param['aid']) {
                    $query->where('aid', $param['aid']);
                }
            });
        foreach ($lists as &$item){
            $item['rechage'] = number_format($item['settle_agent'] + $item['settle_canal'] + $item['settle_admin'],2);
        }
        return $lists;
    }



    public function install($date){
        $data = $this->field(['SUM(`install_ios_settle`) as install_ios_settle',
            'SUM(`install_and_settle`) as install_and_settle',
            'SUM(`install_ios_deduct`) as install_ios_deduct',
            'SUM(`install_and_deduct`) as install_and_deduct'])->where('date',$date)->all();
        return $data[0]['install_ios_settle'] + $data[0]['install_and_settle'] + $data[0]['install_ios_deduct'] + $data[0]['install_and_deduct'];
    }


}
