<?php

namespace App\Model;

use EasySwoole\Mysqli\QueryBuilder;

class Reports extends Base
{
    protected $tableName = 'reports';

    public function list($param = [], $fields = '*')
    {
        $lists = $this->field($fields)
            ->group('hour')
            ->order('hour')
            ->all(function (QueryBuilder $query) use ($param) {
                if (isset($param['date']) && $param['date']) {
                    $query->where('date', $param['date']);
                }
                if (isset($param['cid']) && $param['cid']) {
                    $query->where('cid', $param['cid']);
                }
            });
        return $lists;
    }


}
