<?php

namespace App\HttpController\Canal;

use App\HttpController\BaseController;
use App\Model\Canals;
use App\Utility\JwtToken;
use EasySwoole\Http\Message\Status;
use EasySwoole\Utility\Hash;

class LoginController extends BaseController
{

    public function authorize(){
        try {
            $data = $this->params();
            if(!isset($data['cid']) || !isset($data['aid']) || !$data['cid'] || !$data['aid']){
                return $this->response()->withStatus(Status::CODE_UNAUTHORIZED);
            }
            $model = Canals::create();
            $user = $model->where('status', 1)->where('aid',$data['aid'])->get($data['cid']);
            if(!$user){
                return $this->response()->withStatus(Status::CODE_UNAUTHORIZED);
            }
            $jwtToken = new JwtToken();
            $token = $jwtToken->token($user['id'], 600);
            $return = ['token' => $token, 'username' => $user['username']];
            return $this->writeJson(0,encrypt_data($return),'登录成功');
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, '登录失败，请联系超级管理员');
        }

    }

    public function login()
    {
        try {
            $data = $this->params();
            if(!$data['username']){
                return $this->writeJson(1, null, '请输入账户');
            }
            $user = Canals::create()->get(['username' => $data['username'], 'status' => Canals::STATUS_1]);
            if(!$user){
                return $this->writeJson(1, null, '账户不存在或者被锁定，请联系超级管理员');
            }
            if(Hash::validatePasswordHash($data['password'], $user['password']) == false){
                return $this->writeJson(1, null, '密码输入错误',$user);
            }
            $user->update(['login_ip' => $this->getIp()]);
            $jwtToken = new JwtToken();
            $token = $jwtToken->token($user['id'], 600);//600
            $return = ['token' => $token, 'username' => $user['username']];
            return $this->writeJson(0,encrypt_data($return),'登录成功');
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, '登录失败，请联系超级管理员');
        }
    }



}
