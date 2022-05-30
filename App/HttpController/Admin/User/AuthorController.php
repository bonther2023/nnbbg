<?php

namespace App\HttpController\Admin\User;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Authors;
use Carbon\Carbon;

class AuthorController extends AuthController
{

    public function list()
    {
        try {
            $param = $this->params();
            $param['page'] = (int)$param['page'] ?? 1;
            $param['name'] = (string)$param['name'] ?? '';
            $model = Authors::create();
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
            $model = Authors::create();
            $data['updated_at'] = Carbon::now();
            if ($data['id']) {
                //编辑
                $model->update($data,['id' => $data['id']]);
                return $this->writeJson(0, null, '编辑博主信息成功');
            } else {
                //新增
                $data['created_at'] = Carbon::now();
                $model->data($data)->save();
                return $this->writeJson(0, null, '新增博主信息成功');
            }
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


    public function destroy()
    {
        try {
            $data = $this->params();
            $id = $data['id'] ?? 0;
            $model = Authors::create();
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
            $model = Authors::create();
            $authors = $model->select();
            return $this->writeJson(0, encrypt_data($authors));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

}
