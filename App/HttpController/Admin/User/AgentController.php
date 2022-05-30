<?php

namespace App\HttpController\Admin\User;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Agents;
use App\Model\Trades;
use App\Utility\JwtToken;
use Carbon\Carbon;
use EasySwoole\Utility\Hash;

class AgentController extends AuthController
{

    public function list()
    {
        try {
            $param = $this->params();
            $param['page'] = (int)$param['page'] ?? 1;
            $param['id'] = (int)$param['id'] ?? 0;
            $param['kwd'] = (string)$param['kwd'] ?? '';
            $param['status'] = (int)$param['status'] ?? 0;
            $model = Agents::create();
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
            $model = Agents::create();
            $data['updated_at'] = Carbon::now();
            if ($data['id']) {
                if(!isset($data['balance'])) {
                    //编辑
                    if ($data['password']) {
                        $data['password'] = Hash::makePasswordHash($data['password']);
                    } else {
                        unset($data['password']);
                    }
                }
                $model->update($data,['id' => $data['id']]);
                return $this->writeJson(0, null, '编辑代理信息成功');
            } else {
                //新增
                $data['created_at'] = Carbon::now();
                $data['login_at'] = Carbon::now();
                $data['password'] = Hash::makePasswordHash($data['password'] ?: '123456');
                $model->data($data)->save();
                return $this->writeJson(0, null, '新增代理信息成功');
            }
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function lock()
    {
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?? 0;
            $model = Agents::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->update(['status' => Agents::STATUS_2]);
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function active()
    {
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?? 0;
            $model = Agents::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->update(['status' => Agents::STATUS_1]);
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


    public function destroy()
    {
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?? 0;
            $model = Agents::create();
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
            $model = Agents::create();
            $agents = $model->select();
            return $this->writeJson(0, encrypt_data($agents));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


    public function login(){
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?? 0;
            $model = Agents::create();
            $info = $model->get($id);
            $jwtToken = new JwtToken();
            $token = $jwtToken->token($id);
            $this->response()->setCookie('agent_login', $token, expires(86400 * 7));
            $this->response()->setCookie('agent_user', $info['nickname'], expires(86400 * 7));
            $this->response()->redirect(url_agent('main'));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }



}
