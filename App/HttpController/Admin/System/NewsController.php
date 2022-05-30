<?php

namespace App\HttpController\Admin\System;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\News;
use Carbon\Carbon;

class NewsController extends AuthController
{
    //列表
    public function list()
    {
        try {
            $param = $this->params();
            $param['page'] = (int)$param['page'] ?? 1;
            $model = News::create();
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
            $model = News::create();
            $data['updated_at'] = Carbon::now();
            if ($data['id']) {
                $model->update($data, ['id' => $data['id']]);
                return $this->writeJson(0, null, '编辑消息信息成功');
            } else {
                //新增
                $data['created_at'] = Carbon::now();
                $model->data($data)->save();
                return $this->writeJson(0, null, '新增消息信息成功');
            }
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
            $model = News::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1, null, '抱歉，你要操作的信息不存在');
            $info->destroy();
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


}
