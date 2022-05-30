<?php

namespace App\HttpController\App;


use App\Model\Buys;
use App\Model\Costs;
use App\Model\Users;
use App\Utility\JwtToken;
use Carbon\Carbon;
use EasySwoole\Http\Message\Status;

class UserController extends AuthController
{


    public function index()
    {
        try {
            $model = Users::create();
            //用户信息
            $user = $model->field('id,username,cid,mobile,vip,vip_at,vip_rank,balance,card,code,money,lottery,lottery_free,lottery_date')->get($this->userid);
            if(!$user){
                return $this->writeJson(1);
            }
            if($user['vip'] && $user['vip_at']){
                $now = time();
                if ($now > $user['vip_at']) {
                    $user->update(['vip' => '', 'vip_at' => 0]);
                    $user['vip'] = '';
                    $user['vip_at'] = 0;
                }
            }
            //重置抽奖次数
            $date = Carbon::now()->toDateString();
            $lottery = $user['lottery'];//获取剩余总抽奖次数
            $free = $user['lottery_free'];//免费次数
            if($date != $user['lottery_date']){
                //不等，则重置抽奖次数
                $free = setting('lottery_nvip');
                if($user['vip'] && ($user['vip'] != 'free_vip')){
                    $free = setting('lottery_vip');
                }
                $lottery = setting('lottery_diamond');
                $user->update(['lottery' => $lottery,'lottery_free' => $free, 'lottery_date' => $date]);
            }
            //等级升级提醒
            $rank = $user['vip_rank'] == 7 ? $user['vip_rank'] : $user['vip_rank'] + 1;
            $rankMoney = setting('vip'.$rank.'_price');//下一等级的价格
            $user['show'] = false;
            $user['agio'] = $rankMoney - $user['money'];
            if($user['agio'] < 100){
                $user['show'] = true;
            }else{
                unset($user['agio']);
            }
            unset($user['money']);
            $user['lottery'] = $lottery;
            $user['lottery_free'] = $free;
            $user['vip_at'] = $user['vip_at'] ? date('Y-m-d H:i',$user['vip_at']) : 0;
            $user['forever_link'] = setting('forever_url');
            $user['update_link'] = setting('update_url');
            $user['email'] = setting('email');
            $user['invite_gift'] = setting('invite_gift');
            $user['lottery_nvip'] = setting('lottery_nvip');
            $user['lottery_vip'] = setting('lottery_vip');
            $user['lottery_diamond'] = setting('lottery_diamond');
            $user['lottery_num'] = setting('lottery_num');
            $user['ios'] = 'https://wpuz5.csjshb.cn/vsjmh';
            return $this->writeJson(0, encrypt_data($user));
        } catch (\Throwable $e) {
            write_log('User-index:');
            write_log($this->userid, 4);
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }

    public function code(){
        try {
            $data = $this->params();
            $code = $data['code'] ?? '';
            if(!$code){
                return $this->writeJson(1, null, '参数错误');
            }
            $code = strtoupper($code);
            $model = Users::create();
            $user = $model->get($this->userid);
            if(!$user){
                return $this->writeJson(1);
            }
            if($user['code']){
                return $this->writeJson(1, null, '抱歉，您已绑定过邀请码，请勿重复绑定');
            }
            if($code == $user['card']){
                return $this->writeJson(1, null, '抱歉，您不能绑定自己的邀请码');
            }
            //用户信息
            $parent = $model->where('card',$code)->get();
            if(!$parent){
                return $this->writeJson(1, null, '抱歉，您要绑定的邀请码不存在');
            }
            $user->update(['code' => $code, 'aid' => $parent['aid'], 'cid' => $parent['cid']]);
            //奖励父级用户
            $diamond = setting('invite_gift');
            $model->increase('balance', $parent['id'], $diamond);
            //生成钻石记录
            Costs::create()->add($parent['id'], $diamond, '邀请奖励', 1);
            return $this->writeJson(0,null, '恭喜您，绑定邀请码成功');
        } catch (\Throwable $e) {
            write_log('User-code:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }


    public function mobile(){
        try {
            $data = $this->params();
            $mobile = $data['mobile'] ?? '';
            if(!$mobile){
                return $this->writeJson(1, null, '参数错误');
            }
            $model = Users::create();
            $user = $model->get($this->userid);
            if(!$user){
                return $this->writeJson(1);
            }
            if($user['mobile']){
                return $this->writeJson(1, null, '您已绑定过手机号，请勿重复绑定');
            }
            $old = $model->where('mobile',$mobile)->get();
            if($old){
                return $this->writeJson(1, null, '抱歉，该手机号已经绑定过账户，不能再次绑定账户');
            }
            $user->update(['mobile' => $mobile]);
            return $this->writeJson(0,null, '恭喜您，绑定手机号成功');
        } catch (\Throwable $e) {
            write_log('User-mobile:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }

    public function find()
    {
        try {
            $data = $this->params();
            $mobile = $data['mobile'] ?? '';
            $id = $data['id'] ?? '';
            if (!$mobile || !$id) {
                return $this->writeJson(1, null, '参数错误');
            }
            $model = Users::create();
            $info = $model->where('mobile', $mobile)->where('id', $id)->get();
            if (!$info) {
                return $this->writeJson(1, null, '抱歉，您要找回的账户信息不存在');
            }
            $jwtToken = new JwtToken();
            $token = $jwtToken->token($info['id']);
            $return = [
                'id' => $info['id'],
                'cid' => $info['cid'],
                'username' => $info['username'],
                'uuid' => $info['uuid'],
                'token' => $token
            ];
            return $this->writeJson(0,encrypt_data($return), '恭喜您，成功找回账号信息');
        } catch (\Throwable $e) {
            write_log('User-find:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }

    public function check(){
        try {
            $data = $this->params();
            $version = $data['version'] ?? '';
            $link = setting('update_url');
            $_version = setting('app_version');
            if($_version != $version){
                return $this->writeJson(0,encrypt_data($link));
            }else{
                return $this->writeJson(1, null, '版本一致');
            }
        } catch (\Throwable $e) {
            write_log('User-check:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }


    public function lock(){
        try {
            $model = Users::create();
            $info = $model->field('status')->get($this->userid);
            if($info && $info['status'] == Users::STATUS_2){
                return $this->writeJson(0, null, '锁定');
            }else{
                return $this->writeJson(1, null, '正常');
            }
        } catch (\Throwable $e) {
            write_log('User-lock:');
            write_log($e->getMessage(), 4);
            //这里状态特殊返回一下
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }

    }

    public function record(){
        try {
            $param = $this->params();
            $param['uid'] = $this->userid;
            $model = Costs::create();
            $lists = $model->app($param);
            return $this->writeJson(0,encrypt_data($lists));
        } catch (\Throwable $e) {
            write_log('User-record:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }


    public function buy(){
        try {
            $param = $this->params();
            $param['uid'] = $this->userid;
            $param['type'] = $param['type'] ?? 'qovd';
            $model = Buys::create();
            $lists = $model->app($param);
            return $this->writeJson(0,encrypt_data($lists));
        } catch (\Throwable $e) {
            write_log('User-record:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }

    public function rank(){
        $setting = setting();
        $lists = [
            ['rebate' => $setting['vip0_rebate'], 'name' => '普通'],
            ['rebate' => $setting['vip1_rebate'], 'name' => '青铜'],
            ['rebate' => $setting['vip2_rebate'], 'name' => '白银'],
            ['rebate' => $setting['vip3_rebate'], 'name' => '黄金'],
            ['rebate' => $setting['vip4_rebate'], 'name' => '铂金'],
            ['rebate' => $setting['vip5_rebate'], 'name' => '钻石'],
            ['rebate' => $setting['vip6_rebate'], 'name' => '星耀'],
            ['rebate' => $setting['vip7_rebate'], 'name' => '王者'],
        ];
        return $this->writeJson(0,encrypt_data($lists));
    }


}
