<?php

namespace App\HttpController\Admin\Content;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Images;
use Carbon\Carbon;

class ImageController extends AuthController
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
            $model = Images::create();
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
            $model = Images::create();
            $data['updated_at'] = Carbon::now();
            if ($data['id']) {
                $model->update($data, ['id' => $data['id']]);
                return $this->writeJson(0, null, '编辑套图信息成功');
            } else {
                //新增
                $data['created_at'] = Carbon::now();
                $model->data($data)->save();
                return $this->writeJson(0, null, '新增套图信息成功');
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
            $model = Images::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1, null, '抱歉，你要操作的信息不存在');
            $info->destroy();
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


}
