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
                    $good = $model->good($uvip);
                    break;
                case 2:
                    $good = $model->good($uvip);
                    break;
                case 3:
                    $good = $model->good($uvip);
                    break;
                case 4:
                    $good = $model->good($uvip);
                    break;
                case 5:
                    $good = $model->good($uvip);
                    break;
                case 6:
                    $good = $model->good($uvip);
                    break;
                case 7:
                    $good = $model->good($uvip);
                    break;
                default:
                    $good = $model->good($uvip);
                    break;
            }
            return $this->writeJson(0, encrypt_data($good),$uvip);
        } catch (\Throwable $e) {
            return $this->writeJson(1);
        }
    }



}
