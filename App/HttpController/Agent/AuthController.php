<?php

namespace App\HttpController\Agent;

use App\HttpController\BaseController;
use App\Model\Agents;
use App\Utility\JwtToken;
use EasySwoole\Http\Message\Status;

class AuthController extends BaseController
{

    protected $userid;
    protected $user;

    public function onRequest(?string $action): ?bool
    {
        //判断登录
        $header = $this->request()->getHeaders();
        if (!isset($header['authorization'])) {
            $this->response()->withStatus(Status::CODE_UNAUTHORIZED);
            return false;
        }
        list ($bearer, $token) = explode(' ', $header['authorization'][0]);
        if (!$token) {
            $this->response()->withStatus(Status::CODE_UNAUTHORIZED);
            return false;
        }
        $auth = (new JwtToken())->check($token);
        if ($auth === false) {
            $this->response()->withStatus(Status::CODE_UNAUTHORIZED);
            return false;
        }
        $user = Agents::create()->field('username,name,qq,telegram,bank,card,type,address,balance')
            ->where('status',1)->get($auth);
        if(!$user){
            $this->response()->withStatus(Status::CODE_UNAUTHORIZED);
            return false;
        }
        $this->userid = $auth;
        $this->user = $user;
        return true;
    }




}
