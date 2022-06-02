<?php

namespace App\HttpController\App;

use App\Model\Videos;

class VideoController extends AuthController
{

    public function index()
    {
        try {
            $param = $this->params();
            //判断用户VIP等级，返回对应等级的数据
            $uvip = $param['vip'] ?? 0;
            $model = Videos::create();
            switch ($uvip){
                case 1:
                    $good = $model->good(20);
                    break;
                case 2:
                    $good = $model->good(20);
                    break;
                case 3:
                    $good = $model->good(20);
                    break;
                case 4:
                    $good = $model->good(20);
                    break;
                case 5:
                    $good = $model->good(20);
                    break;
                case 6:
                    $good = $model->good(20);
                    break;
                case 7:
                    $good = $model->good(20);
                    break;
                case 8:
                    $good = $model->good(20);
                    break;
                case 9:
                    $good = $model->good(20);
                    break;
                default:
                    $good = $model->good(20);
                    break;
            }
            return $this->writeJson(0, encrypt_data($good));
        } catch (\Throwable $e) {
            return $this->writeJson(1);
        }
    }



}
