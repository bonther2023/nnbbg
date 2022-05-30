<?php

namespace App\HttpController\App;


use App\HttpController\BaseController;
use App\Model\Canals;
use App\Model\Costs;
use App\Model\Users;
use App\Utility\JwtToken;
use Carbon\Carbon;

class LoginController extends BaseController
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
                }
            }
        }
        return true;
    }



    public function index()
    {
        try {
            //注册
            $data = $this->params();
            $data['canalid'] = (int)$data['canalid'] ?? 5000;
            $data['uuid'] = $data['uuid'] ?? '1234567';
            $data['username'] = $data['username'] ?? '用户_UNKONWN';
            $data['code'] = $data['code'] ?? '';
            $userModel = Users::create();
            if($this->userid){
                //token登录
                //查找用户数据
                $user = $userModel->get($this->userid);
                //如果用户数据存在，直接登录
                if($user){
                    $return = $this->login($user,$data);
                    return $this->writeJson(0, encrypt_data([
                        'username' => $return['username'],
                        'cid' => $return['cid'],
                        'token' => $return['token'],
                    ]));
                }
            }
            //如果用户数据不存在，根据uuid查数据
            $user = $userModel->where('uuid', $data['uuid'])->get();
            //如果用户数据存在，一样直接登录
            if($user){
                $return = $this->login($user,$data);
                return $this->writeJson(0, encrypt_data([
                    'username' => $return['username'],
                    'cid' => $return['cid'],
                    'token' => $return['token']
                ]));
            }

            //既没有token又没有uuid所对应的数据，则注册用户
            $return = $this->register($data);
            return $this->writeJson(0, encrypt_data([
                'username' => $return['username'],
                'cid' => $return['cid'],
                'token' => $return['token']
            ]));
        } catch (\Throwable $e) {
            write_log('Login-index:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }


    protected function canal($cid = 0){
        $canalModel = Canals::create();
        $canal = $canalModel->field('id,aid,rebate_register,num_order')->get($cid);
        if($canal){
            $aid = $canal['aid'];
            $cid = $canal['id'];
            $rebate = $canal['rebate_register'];
        }else{
            $aid = 1000;
            $cid = 5000;
            $rebate = 100;
        }
        return ['aid' => $aid, 'cid' => $cid, 'rebate_register' => $rebate];
    }


    protected function login($user,$data){
        //更新uuid
        $user->update(['uuid' => $data['uuid']]);
        //更新用户注册信息
        $time = Carbon::now();
        $ip = $this->getIp();
        $this->queueUser([
            'info' => [
                'uid'      => $user['id'],
                'version'  => $data['version'],
                'model'    => $data['model'],
                'vendor'   => $data['vendor'],
                'release'  => $data['release'],
                'ip'       => $ip,
                'network'  => $data['network'],
                'system'   => $data['system'],
                'time'     => $time
            ],
            'login' => true,
        ]);
        $token = $this->token($user['id']);
        return [
            'token' => $token,
            'username' => $user['username'],
            'cid' => $user['cid'],
        ];
    }

    protected function register($data){
        $cid = $data['canalid'];
        $userModel = Users::create();
        //邀请码存在，则渠道信息是邀请人的信息
        //ctype_alnum 判断是否是数字字母组合
        if($data['code'] && is_string($data['code']) && ctype_alnum($data['code']) && trim($data['code'])){
            $parent = $userModel->where('card', $data['code'])->get();
            if($parent){
                $cid = $parent['cid'];
                //赠送邀请人钻石
                //奖励父级用户
                $diamond = setting('invite_gift');
                $userModel->increase('balance', $parent['id'], $diamond);
                //生成钻石记录
                Costs::create()->add($parent['id'], $diamond, '邀请奖励', 1);
            }else{
                $data['code'] = '';
            }
        }else{
            $data['code'] = '';
        }
        $ip = $this->getIp();
        $time = Carbon::now();
        $canalInfo = $this->canal($cid);
        $userId = $userModel->data([
            'aid'   => $canalInfo['aid'],
            'uuid'  => $data['uuid'],
            'cid'   => $canalInfo['cid'],
            'code' => $data['code'],
            'username'   => $data['username'],
            'vip'        => 'free_vip',
            'vip_at'     => time() + setting('free_vip_time') * 60,
            'created_at' => $time,
            'updated_at' => $time,
        ])->save();
        //更新flow
        $this->queueFlow([
            'info' => [
                'aid'  => $canalInfo['aid'],
                'cid'  => $canalInfo['cid'],
                'date' => $time->toDateString(),
                'rebate_register'   => $canalInfo['rebate_register'],
                'system'   => $data['system'] == 'Android' ? 1 : 2,
            ],
            'install' => true,
        ]);
        //更新report
        $this->queueReport([
            'info' => [
                'aid' => $canalInfo['aid'],
                'cid' => $canalInfo['cid'],
                'system' => $data['system'] == 'Android' ? 1 : 2,
                'date' => $time->toDateString(),
                'hour' => $time->hour,
            ],
            'install' => true,
        ]);
        //更新用户
        $this->queueUser([
            'info' => [
                'uid'      => $userId,
                'version'  => $data['version'],
                'model'    => $data['model'],
                'vendor'   => $data['vendor'],
                'release'  => $data['release'],
                'ip'       => $ip,
                'network'  => $data['network'],
                'system'   => $data['system'],
                'time'     => $time
            ],
            'register' => true,
        ]);
        $token = $this->token($userId);
        return ['username' => $data['username'], 'cid' => $canalInfo['cid'], 'token' => $token];
    }

    protected function token($uid){
        $jwtToken = new JwtToken();
        return $jwtToken->token($uid);
    }


}
