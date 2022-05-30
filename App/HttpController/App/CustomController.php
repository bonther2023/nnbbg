<?php

namespace App\HttpController\App;

use App\Model\Customs;
use App\Model\Users;
use Carbon\Carbon;

class CustomController extends AuthController
{
    public function index(){
        try {
            if(!$this->userid){
                return $this->writeJson(1, null, '参数错误');
            }
            $userModel = Users::create();
            $user = $userModel->get($this->userid);
            if(!$user){
                return $this->writeJson(1, null, '非法请求');
            }
            $userInfo = [
                'username' => $user['username'],
                'uid' => $user['id'],
                'mobile' => $user['mobile'],
                'ip' => $user['ip'],
                'address' => $user['address'],
                'vip' => $user['vip'],
                'vip_at' => date('Y-m-d H:i:s',$user['vip_at']),
                'app_system' => $user['app_system'],
                'app_model' => $user['app_model'],
                'app_network' => $user['app_network'],
                'app_vendor' => $user['app_vendor'],
                'app_release' => $user['app_release'],
                'reg_at' => $user['created_at'],
                'created_at' => Carbon::now(),
            ];
            $customModel = Customs::create();
            $info = $customModel->where('uid', $user['id'])->get();
            if($info){
                $info->update($userInfo);
            }else{
                $customModel->data($userInfo)->save();
            }
            $socket = trim(setting('socket_link'),'/');
            $forever = trim(setting('forever_url'),'/');
            $upload = trim(setting('custom_link'),'/').'/api/upload/image';
            return $this->writeJson(0, encrypt_data([
                'user' => $userInfo,
                'socket' => $socket,
                'forever' => $forever,
                'upload' => $upload
            ]));
        } catch (\Throwable $e) {
            write_log('Custom-index:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }



}
