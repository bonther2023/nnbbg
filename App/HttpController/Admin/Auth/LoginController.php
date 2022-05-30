<?php

namespace App\HttpController\Admin\Auth;

use App\HttpController\BaseController;
use App\Model\Admins;
use App\Utility\JwtToken;
use EasySwoole\Utility\Hash;

class LoginController extends BaseController
{

    public function login()
    {
        try {
            $param = $this->params();
            $user = Admins::create()->get(['username' => $param['username'], 'status' => Admins::STATUS_1]);
            if(!$user){
                return $this->writeJson(1, null, '管理员账户不存在或者被锁定，请联系超级管理员');
            }
            if(Hash::validatePasswordHash($param['password'], $user['password']) == false){
                return $this->writeJson(1, null, '密码输入错误');
            }
//            $settings = settings();
//            if($settings['login_limit']){
//                //验证登录用户IP是否合法
//                $ip = $this->getIp();
//                $result = (new IpLookup())->getInfo($ip,0);
//                if(!$result || $result['country'] !== '柬埔寨'){
//                    return $this->writeJson(1, null, '非法登录');
//                }
//            }
            $jwtToken = new JwtToken();
            $token = $jwtToken->token($user['id']);
            $return = ['token' => $token, 'username' => $user['nickname']];
            return $this->writeJson(0,encrypt_data($return),'登录成功,页面即将跳转');
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }
}
