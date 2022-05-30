<?php

namespace App\HttpController\App;

use App\HttpController\BaseController;
use App\Model\Users;
use App\Utility\JwtToken;
use EasySwoole\RedisPool\RedisPool;

class AuthController extends BaseController
{

    protected $userid = 0;

    public function onRequest(?string $action): ?bool
    {
        //判断登录
        $header = $this->request()->getHeaders();
        if (isset($header['authorization']) && $header['authorization']) {
            list ($bearer, $token) = explode(' ', $header['authorization'][0]);
            if ($token) {
                $auth = (new JwtToken())->check($token);
                if ($auth !== false) {
                    $this->userid = $auth;
                    $this->online($this->userid);
                }
            }
        }
        return true;
    }

    //用户是否是VIP，免费的也算
    protected function vip()
    {
        $vip = false;
        if ($this->userid) {
            $userModel = Users::create();
            //查看用户VIP信息
            $user = $userModel->field('vip,vip_at')->get($this->userid);
            if ($user && $user['vip'] && $user['vip_at']) {
                $now = time();
                if ($now > $user['vip_at']) {
                    //清除VIP信息
                    $userModel->clearVip($this->userid);
                    $user['vip'] = '';
                }
                if ($user['vip']) {
                    $vip = true;
                }
            }
        }
        return $vip;
    }

    //用户是否是月卡及以上
    protected function svip()
    {
        $svip = false;
        if ($this->userid) {
            $userModel = Users::create();
            //查看用户VIP信息
            $user = $userModel->field('vip,vip_at')->get($this->userid);
            if ($user && $user['vip'] && $user['vip_at']) {
                $now = time();
                if ($now > $user['vip_at']) {
                    //清除VIP信息
                    $userModel->clearVip($this->userid);
                    $user['vip'] = '';
                }
                //免费的/一日的不算
                if ($user['vip'] && ($user['vip'] != 'free_vip') && ($user['vip'] != 'day_vip')) {
                    $svip = true;
                }
            }
        }
        return $svip;
    }

    protected function rank()
    {
        $rank = 0;
        if ($this->userid) {
            $userModel = Users::create();
            //查看用户VIP信息
            $user = $userModel->field('vip_rank')->get($this->userid);
            $rank = $user['vip_rank'];
        }
        return $rank;
    }


    //统计在线人数
    protected function online($uid)
    {
        $redis = RedisPool::defer();
        $time = (int)time();
        //向有序集合添加一个成员，或者更新已存在成员的登录时间
        //这个时间值用户只要在线请求接口就会更新
        $redis->zAdd('online', $time, (string)$uid);
        //如果固定时间内这个值不变，则代表用户不在线
        //如果这个值低于某个数值的时候，则清除该用户数据
        $_time = $time - (int)(setting('online_time') * 60);
        $redis->zRemRangeByScore('online', 0, $_time);
    }


    protected function build()
    {
        $order_id_main = date('YmdHis') . rand(1000,9999);
        $order_id_len = strlen($order_id_main);
        $order_id_sum = 0;
        for($i=0; $i<$order_id_len; $i++){
            $order_id_sum += (int)(substr($order_id_main,$i,1));
        }
        return $order_id_main . str_pad((100 - $order_id_sum % 100) % 100,2,'0',STR_PAD_LEFT);
    }


}
