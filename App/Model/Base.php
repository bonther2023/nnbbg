<?php

namespace App\Model;

use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\AbstractModel;

class Base extends AbstractModel
{

    public function paginate($data, $page = 1, $perPage = 10)
    {
        $total = $this->lastQueryResult()->getTotalCount();
        return [
            'data' => $data,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
            'per_page' => $perPage,
            'current_page' => $page,
        ];
    }


    //APP 字段自增
    public function increase($field = '',$id = 0, $num = 1){
        return $this->update([$field => QueryBuilder::inc($num)],['id' => $id]);
    }

    //APP 字段自减
    public function decrease($field = '',$id = 0, $num = 1){
        return $this->update([$field => QueryBuilder::dec($num)],['id' => $id]);
    }




}
