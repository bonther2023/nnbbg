<?php

namespace App\HttpController\Admin\System;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Orders;
use App\Model\Pays;
use App\Model\Users;
use App\Utility\Pay;
use Carbon\Carbon;
use EasySwoole\RedisPool\RedisPool;

class PayController extends AuthController
{

    function list() {
        try {
            $param = $this->params();
            $param['page'] = (int)$param['page'] ?? 1;
            $param['kwd'] = (string)$param['kwd'] ?? '';
            $param['status'] = (int)$param['status'] ?? 0;
            $model  = Pays::create();
            $lists  = $model->list($param);
            return $this->writeJson(0, encrypt_data(['lists' => $lists,'notify' => '']));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function update()
    {
        try {
            $data = $this->params();
            $model  = Pays::create();
            $data['updated_at'] = Carbon::now();
            $info = $model->where('name', $data['name'])->where('id', $data['id'], '<>')->get();
            if($info){
                return $this->writeJson(1, null, '支付标识已存在，请重新填写');
            }
            if ($data['id']) {
                $model->update($data,['id' => $data['id']]);
                return $this->writeJson(0, null, '编辑支付信息成功');
            } else {
                //新增
                $data['created_at'] = Carbon::now();
                $model->data($data)->save();
                return $this->writeJson(0, null, '新增支付信息成功');
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
            $model = Pays::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(0,null,'抱歉，你要操作的信息不存在');
            $info->update(['status' => Pays::STATUS_2]);
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
            $model = Pays::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(0,null,'抱歉，你要操作的信息不存在');
            $info->update(['status' => Pays::STATUS_1]);
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
            $model  = Pays::create();
            $info    = $model->get($id);
            if (empty($info)) return $this->writeJson(0,null,'抱歉，你要操作的信息不存在');
            $info->destroy();
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function select(){
        try {
            $model  = Pays::create();
            $pays  = $model->select();
            return $this->writeJson(0, encrypt_data($pays));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


}
