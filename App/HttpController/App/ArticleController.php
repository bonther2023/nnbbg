<?php

namespace App\HttpController\App;

use App\Model\Buys;
use App\Model\Articles;

class ArticleController extends AuthController
{


    public function index()
    {
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            $param['kwd'] = $param['kwd'] ?? '';
            $model = Articles::create();
            $fields = 'id,title,cid,thumb,summary,money,view,focus,created_at';
            $data = $model->list($param,$fields,10);
            return $this->writeJson(0,  encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Article-index:');
            write_log($e->getMessage(),4);
            return $this->writeJson(1);
        }
    }

    public function info()
    {
        try {
            $param = $this->params();
            $id = $param['id'] ?? 0;
            if(!$id){
                return $this->writeJson(1);
            }
            $model = Articles::create();
            $info = $model->info($id);
            if(!$info){
                return $this->writeJson(1);
            }
            //增加浏览量
            $model->increase('view', $id);
            //默认未关注
            $info['follow'] = false;
            //用户是否是月卡及以上
            $info['isvip'] = $this->svip();
            //查看会员是否已经购买
            $info['isbuy'] = true;
            if($info['money'] > 0){
                $buy = Buys::create()->buy($this->userid,'article', $id);
                $info['isbuy'] = $buy;
                //如果用户购买过，不是月卡及以上也能观看
                $info['isvip'] = $buy ? $buy : $info['isvip'];
            }
            return $this->writeJson(0, encrypt_data($info));
        } catch (\Throwable $e) {
            write_log('Article-info:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }

}
