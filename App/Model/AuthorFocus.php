<?php

namespace App\Model;

use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\RedisPool\RedisPool;

class AuthorFocus extends Base
{
    protected $tableName = 'author_focus';


    public function add($aid = 0, $uid = 0){
        $info = $this->where('aid', $aid)->where('uid',$uid)->get();
        if($info){
            return true;
        }
        return $this->data(['aid' => $aid, 'uid' => $uid])->save();
    }


    public function focus($aid = 0, $uid = 0){
        $info = $this->where('aid', $aid)->where('uid',$uid)->get();
        return $info ? true : false;
    }

}
