<?php

namespace App\HttpController\Admin\User;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Users;
use Carbon\Carbon;

class UserController extends AuthController
{

    //TODO 用户注册，首先先注册主要的基本信息，其他信息的绑定，录入，放到队列中去处理
    //TODO 后台禁用用户，前台直接退出APP
    public function list()
    {
        try {
            $param = $this->params();
            $param['page'] = (int)$param['page'] ?? 1;
            $param['cid'] = (int)$param['cid'] ?? 0;
            $param['mobile'] = (string)$param['mobile'] ?? '';
            $param['id'] = (int)$param['id'] ?? 0;
            $param['kwd'] = (string)$param['kwd'] ?? '';
            $param['system'] = (string)$param['system'] ?? '';
            $model = Users::create();
            $lists = $model->list($param);
            return $this->writeJson(0,encrypt_data($lists));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


    public function update()
    {
        try {
            $data = $this->params();
            $model = Users::create();
            $user = $model->get($data['id']);
            if (empty($user)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            if(!isset($data['balance'])) {
                if($data['vip_at']){
                    $data['vip_at'] = strtotime($data['vip_at']);
                }
            }
            $user->update($data);
            return $this->writeJson(0,null,'更新用户信息成功');
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function lock()
    {
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?: 0;
            $model = Users::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->update(['status' => Users::STATUS_2]);
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
            $model = Users::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->update(['status' => Users::STATUS_1]);
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
            $model = Users::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->destroy();
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

}
