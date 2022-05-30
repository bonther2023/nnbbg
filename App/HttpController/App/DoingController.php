<?php

namespace App\HttpController\App;


use App\Model\Bais;
use App\Model\Doings;
use App\Model\Users;
use Carbon\Carbon;

class DoingController extends AuthController
{


    public function index()
    {
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            $model = Doings::create();
            $lists = $model->app($param);
            return $this->writeJson(0, encrypt_data($lists));
        } catch (\Throwable $e) {
            write_log('Doing-index:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }

    public function whore(){
        try {
            //上个月所有中奖的
            $time = Carbon::now();
            $day = $time->format('d');
            $date = $time->format('Y-m');
            $subDate = $time->subMonth()->format('Y-m');
            $model = Bais::create();
            //10号之后不查开奖记录
            if($day <= 10){
                $lists = $model->app($subDate);
            }else{
                $lists = $model->app($date);
            }
            //这个月领取的白嫖码
            $info = $model->field('code')->where('date',$date)->where('uid',$this->userid)->get();
            $code = $info ? $info['code'] : '';
            return $this->writeJson(0, encrypt_data([
                'code' => $code,
                'lists' => $lists,
            ]));
        } catch (\Throwable $e) {
            write_log('Doing-whore:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }


    public function receive(){
        try {
            $model = Bais::create();
            $time = Carbon::now();
            $date = $time->format('Y-m');
            $info = $model->field('code')->where('date',$date)->where('uid',$this->userid)->get();
            if($info){
                return $this->writeJson(1, null, '这月您已领取，请勿重复领取');
            }
            $userModel = Users::create();
            //查看用户VIP信息
            $user = $userModel->field('id,username,vip')->get($this->userid);
            if ($user && $user['vip'] &&  ($user['vip'] == 'year_vip' || $user['vip'] == 'forever_vip')) {
                $code = $model->code($date);
                $model->data([
                    'date' => $date,
                    'uid' => $user['id'],
                    'username' => $user['username'],
                    'code' => $code,
                ])->save();
                return $this->writeJson(0, encrypt_data($code),'领取成功');
            }
            return $this->writeJson(1, null, '抱歉，您的会员等级不足');
        } catch (\Throwable $e) {
            write_log('Doing-receive:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }



}
