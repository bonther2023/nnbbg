<?php

namespace App\HttpController\App;

use App\Model\News;

class NewsController extends AuthController
{


    public function index()
    {
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            $model = News::create();
            $fields = 'id,thumb,html,focus,link,created_at';
            $data = $model->list($param,$fields,10);
            return $this->writeJson(0,  encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('News-index:');
            write_log($e->getMessage(),4);
            return $this->writeJson(1);
        }
    }

}
