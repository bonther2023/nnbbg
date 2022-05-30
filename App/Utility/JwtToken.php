<?php
namespace App\Utility;

use EasySwoole\Jwt\Jwt;

class JwtToken{

    protected $jwt;
    protected $secret = 'jwt20211010123456789zbcdefg';

    public function __construct()
    {
        $this->jwt = Jwt::getInstance();
    }

    /**
     * 获取token
     * @param $data
     * @param int $time
     * @return false|string
     */
    public function token($data, $time = 86400 * 30){
        $obj = $this->jwt->setSecretKey($this->secret)->publish();
        $obj->setAud('user'); // 用户
        $obj->setExp(time() + $time); // 过期时间
        $obj->setIat(time()); // 发布时间
        $obj->setIss('user'); // 发行人
        $obj->setJti(md5(time())); // jwt id 用于标识该jwt
        $obj->setSub('token'); // 主题

        // 自定义数据
        $obj->setData($data);

        // 最终生成的token
        $token = $obj->__toString();
        return $token;
    }

    public function check($token){
        try{
            $obj = $this->jwt->setSecretKey($this->secret)->decode($token);
            $status = $obj->getStatus();
            if($status > 0){
                return $obj->getData();
            }
            return false;
        }catch (\Exception $e){
            return false;
        }
    }
}
