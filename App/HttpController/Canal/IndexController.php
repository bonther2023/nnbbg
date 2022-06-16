<?php

namespace App\HttpController\Canal;

use App\Model\Canals;
use App\Model\Flows;
use App\Model\Trades;
use Carbon\Carbon;
use EasySwoole\Utility\Hash;

class IndexController extends AuthController {

    public function index()
    {
        try {
            $date = Carbon::now()->toDateString();
            $model = Flows::create();
            //订单佣金
            $rebate = $model->where('cid',$this->userid)->where('date',$date)->sum('settle_canal');

            //订单效果
            $iosInstall = $model->where('cid',$this->userid)->where('date',$date)->sum('install_ios_settle');
            $andInstall = $model->where('cid',$this->userid)->where('date',$date)->sum('install_and_settle');
            $webLink = trim(setting('web_link'),'/');
            $apkLink = trim(setting('apk_link'),'/').'/'.$this->userid.'.apk';
            return $this->writeJson(0, encrypt_data([
                'rebate' => $rebate,
                'install' => $iosInstall + $andInstall,
                'web' => $webLink, 'apk' => $apkLink
            ]));
        } catch (\Throwable $e) {
            return $this->writeJson(1);
        }

    }

    public function user()
    {
        return $this->writeJson(0, encrypt_data($this->user));
    }

    public function flow(){
        try {
            $data = $this->params();
            $params = [
                'page' => $data['page'] ?? 1,
                'start' => $data['start'] ?? '',
                'end' => $data['end'] ?? '',
                'cid' => $this->userid,
            ];
            $model = Flows::create();
            $fields = 'date,install_ios_settle,install_and_settle,settle_canal';
            $lists = $model->user($params, $fields, 10);
            return $this->writeJson(0, encrypt_data($lists));
        } catch (\Throwable $e) {
            return $this->writeJson(1);
        }
    }

    public function trade(){
        try {
            $data = $this->params();
            $param = [
                'page' => $data['page'] ?? 1,
                'start' => $data['start'] ?? '',
                'end' => $data['end'] ?? '',
                'userid' => $this->userid,
                'role' => 1,
            ];
            $model = Trades::create();
            $fields = 'date,money,status';
            $lists = $model->list($param, $fields);
            return $this->writeJson(0, encrypt_data($lists));
        } catch (\Throwable $e) {
            return $this->writeJson(1);
        }
    }


    public function update()
    {
        try {
            $data = $this->params();
            $model = Canals::create();
            $info = $model->get($this->userid);
            if($data['type'] && $data['card']){
                return $this->writeJson(1, null, '银行卡信息和USDT信息只能填写其中一样');
            }
            $info->update($data);
            return $this->writeJson(0, null, '更新成功');
        } catch (\Throwable $e) {
            return $this->writeJson(1);
        }
    }

    public function password()
    {
        try {
            $data = $this->params();
            $model = Canals::create();
            $info = $model->get($this->userid);
            if(!$data['new_password']){
                return $this->writeJson(1, null, '请输入新密码');
            }
            if($data['old_password'] == $data['new_password']){
                return $this->writeJson(1, null, '新旧密码输入一致');
            }
            if($data['new_password'] != $data['check_password']){
                return $this->writeJson(1, null, '确认密码输入不一致');
            }
            if(Hash::validatePasswordHash($data['old_password'], $info['password']) == false){
                return $this->writeJson(1, null, '旧密码输入错误');
            }
            $info->update(['password' => Hash::makePasswordHash($data['new_password'])]);
            return $this->writeJson(0, null, '修改密码成功');
        } catch (\Throwable $e) {
            return $this->writeJson(1);
        }
    }


    public function sort(){
        try {
            $target = trim(setting('web_link'),'/');
            $domain = [
                $target."/o.php?auth=".base64_encode('/hu/lou/index.html?qdid='.$this->userid),
                $target."/o.php?auth=".base64_encode('/hu/zhi/index.html?qdid='.$this->userid),
                $target."/o.php?auth=".base64_encode('/hu/you/index.html?qdid='.$this->userid),
            ];
            return $this->writeJson(0, encrypt_data($domain));
        } catch (\Throwable $e) {
            return $this->writeJson(1);
        }
    }





}
