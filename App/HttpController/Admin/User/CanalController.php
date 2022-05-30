<?php

namespace App\HttpController\Admin\User;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Canals;
use App\Model\Trades;
use App\Utility\JwtToken;
use Carbon\Carbon;
use EasySwoole\Utility\Hash;

class CanalController extends AuthController
{

    public function list()
    {
        try {
            $param = $this->params();
            $param['page'] = (int)$param['page'] ?? 1;
            $param['aid'] = (int)$param['aid'] ?? 0;
            $param['id'] = (int)$param['id'] ?? 0;
            $param['kwd'] = (string)$param['kwd'] ?? '';
            $param['status'] = (int)$param['status'] ?? 0;
            $model = Canals::create();
            $lists = $model->list($param);
            return $this->writeJson(0, encrypt_data($lists));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function update()
    {
        try {
            $data = $this->params();
            $model = Canals::create();
            $data['updated_at'] = Carbon::now();
            if(!isset($data['balance'])){
                //判断分成设置是不是5的倍数  percent_canal   percent_agent
                if($data['percent_canal']%5 || $data['percent_canal'] > 95){
                    return $this->writeJson(1,null, '请设置渠道分成为5的倍数且最大数不能超过95');
                }
                $data['percent_agent'] = 95 - $data['percent_canal'];
            }
            if ($data['id']) {
                if(!isset($data['balance'])){
                    //编辑
                    if ($data['password']) {
                        $data['password'] = Hash::makePasswordHash($data['password']);
                    } else {
                        unset($data['password']);
                    }
                }
                $model->update($data,['id' => $data['id']]);
                return $this->writeJson(0, null, '编辑渠道信息成功');
            } else {
                //新增
                $data['created_at'] = Carbon::now();
                $data['login_at'] = Carbon::now();
                $data['password'] = Hash::makePasswordHash($data['password'] ?: '123456');
                $model->data($data)->save();
                return $this->writeJson(0, null, '新增渠道信息成功');
            }
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


    public function lock()
    {
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?: 0;
            $model = Canals::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->update(['status' => Canals::STATUS_2]);
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


    public function active()
    {
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?: 0;
            $model = Canals::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->update(['status' => Canals::STATUS_1]);
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


    public function destroy()
    {
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?: 0;
            $model = Canals::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->destroy();
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function select()
    {
        try {
            $model = Canals::create();
            $canals = $model->select();
            return $this->writeJson(0, encrypt_data($canals));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }



    public function login(){
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?: 0;
            $model = Canals::create();
            $info = $model->get($id);
            $jwtToken = new JwtToken();
            $token = $jwtToken->token($id);
            $this->response()->setCookie('canal_login', $token, expires(86400 * 7));
            $this->response()->setCookie('canal_user', $info['nickname'], expires(86400 * 7));
            $this->response()->redirect(url_canal('main'));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


}
