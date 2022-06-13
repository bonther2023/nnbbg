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



    public function index()
    {
        try {
            //注册
            $data = $this->params();
            $data['canalid'] = (int)$data['canalid'] ?? 5000;
            $this->register($data);
            return $this->writeJson();
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
            $aid = 0;
            $cid = $canal['id'];
            $rebate = $canal['rebate_register'];
        }else{
            $aid = 0;
            $cid = 5000;
            $rebate = 100;
        }
        return ['aid' => $aid, 'cid' => $cid, 'rebate_register' => $rebate];
    }

    protected function register($data){
        $cid = $data['canalid'];
        $time = Carbon::now();
        $canalInfo = $this->canal($cid);
        //更新flow
        $this->queueFlow([
            'info' => [
                'aid'  => 0,
                'cid'  => $cid,
                'date' => $time->toDateString(),
                'rebate_register'   => $canalInfo['rebate_register'],
                'system'   => 1,
            ],
            'install' => true,
        ]);
        //更新report
        $this->queueReport([
            'info' => [
                'aid' => 0,
                'cid' => $canalInfo['cid'],
                'system' => 1,
                'date' => $time->toDateString(),
                'hour' => $time->hour,
            ],
            'install' => true,
        ]);
    }

    protected function token($uid){
        $jwtToken = new JwtToken();
        return $jwtToken->token($uid);
    }


}
