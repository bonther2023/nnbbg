<?php

namespace App\HttpController\Admin\Other;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Games;
use Carbon\Carbon;

class GameController extends AuthController
{

    //列表
    public function list()
    {
        try {
            $param = $this->params();
            $param['page'] = (int)$param['page'] ?? 1;
            $param['status'] = (int)$param['status'] ?? 0;
            $model = Games::create();
            $lists = $model->list($param);
            return $this->writeJson(0, encrypt_data($lists));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    //新增/更新
    public function update()
    {
        try {
            $data = $this->params();
            $model = Games::create();
            $data['updated_at'] = Carbon::now();
            if ($data['id']) {
                $model->update($data,['id' => $data['id']]);
                return $this->writeJson(0, null, '编辑游戏信息成功');
            } else {
                //新增
                $data['created_at'] = Carbon::now();
                $model->data($data)->save();
                return $this->writeJson(0, null, '新增游戏信息成功');
            }
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    //锁定
    public function lock()
    {
        try {
            $data = $this->params();
            $id = $data['id'] ?? 0;
            $model = Games::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->update(['status' => Games::STATUS_2]);
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    //激活
    public function active()
    {
        try {
            $data = $this->params();
            $id = $data['id'] ?? 0;
            $model = Games::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->update(['status' => Games::STATUS_1]);
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    //删除
    public function destroy()
    {
        try {
            $data = $this->params();
            $id = $data['id'] ?? 0;
            $model = Games::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->destroy();
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


}
