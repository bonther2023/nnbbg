<?php

namespace App\HttpController\Admin\Content;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Contes;
use Carbon\Carbon;

class ConteController extends AuthController
{
    //列表
    public function list()
    {
        try {
            $param = $this->params();
            $param['page'] = (int)$param['page'] ?? 1;
            $param['cid'] = (int)$param['cid'] ?? 0;
            $param['status'] =(int)$param['status'] ?? 0;
            $param['kwd'] = (string)$param['kwd'] ?? '';
            $model = Contes::create();
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
            $model = Contes::create();
            $data['updated_at'] = Carbon::now();
            if ($data['id']) {
                $model->update($data, ['id' => $data['id']]);
                return $this->writeJson(0, null, '编辑短篇小说信息成功');
            } else {
                //新增
                $data['created_at'] = Carbon::now();
                $model->data($data)->save();
                return $this->writeJson(0, null, '新增短篇小说信息成功');
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
            $id = (int)$data['id'] ?? 0;
            $model = Contes::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1, null, '抱歉，你要操作的信息不存在');
            $info->destroy();
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


}
