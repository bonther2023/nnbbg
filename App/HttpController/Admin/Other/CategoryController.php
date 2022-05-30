<?php

namespace App\HttpController\Admin\Other;


use App\HttpController\Admin\Auth\AuthController;
use App\Model\Categorys;
use Carbon\Carbon;

class CategoryController extends AuthController
{
    //列表
    public function list()
    {
        try {
            $param = $this->params();
            $model = Categorys::create();
            $param['page'] = $param['page'] ?? 1;
            $lists = $model->list($param);
            return $this->writeJson(0, encrypt_data($lists));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    //类型
    public function type()
    {
        try {
            $model = Categorys::create();
            $lists = $model::TYPE_TEXT;
            foreach ($lists as &$type){
                $type = strip_tags($type);
            }
            return $this->writeJson(0, encrypt_data($lists));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    //类目
    public function select()
    {
        try {
            $param = $this->params();
            $model = Categorys::create();
            $param['type'] = $param['type'] ?? 1;
            $lists = $model->select($param['type']);
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
            $model = Categorys::create();
            $data['updated_at'] = Carbon::now();
            if ($data['id']) {
                $model->update($data,['id' => $data['id']]);
                return $this->writeJson(0, null, '编辑类目成功');
            } else {
                //新增
                $data['created_at'] = Carbon::now();
                $model->data($data)->save();
                return $this->writeJson(0, null, '新增类目成功');
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
            $model = Categorys::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->destroy();
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

}
