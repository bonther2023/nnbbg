<?php

namespace App\HttpController\Admin\Other;

use App\HttpController\BaseController;
use App\Utility\Filesystem;

class UploadController extends BaseController
{
    /**
     * 图片上传
     * @return bool
     */
    public function image(){
        try{
            $request = $this->request();
            $files = $request->getUploadedFile('image');
            $return = (new Filesystem($files))->storeAs();
            return $this->writeJson(0,$return);
        }catch (\Throwable $e){
            return $this->writeJson(1,null,$e->getMessage());
        }
    }

    /**
     * 文件上传
     * @return bool
     */
    public function file(){
        try{
            $request = $this->request();
            $files = $request->getUploadedFile('file');
            $return = (new Filesystem($files))->storeAs();
            return $this->writeJson(0,$return);
        }catch (\Throwable $e){
            return $this->writeJson(1,null,$e->getMessage());
        }
    }

    /**
     * base64位图片上传
     * @return bool
     */
    public function img(){
        try{
            $request = $this->request();
            $img = (string)trim($request->getRequestParam('img')) ?: '';
            $img = str_replace(' ', '+', $img);
            if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img, $result)){
                $time = date('Y-m-d');
                $ext = $result[2];
                $name = hash('md5', $img);
                $dirname = 'upload';
                $fileName = $dirname . '/' . $time . '/' . $name . '.' . $ext;
                $filePath = trim(config('PUBLIC_ROOT'), '/') . '/' . $fileName;
                file_put_contents($filePath, base64_decode(str_replace($result[1], '', $img)));
                $domain = trim(config('SERVER_URL'), '/');
                $return = $domain . '/' . $fileName;
                return $this->writeJson(0,$return);
            }
        }catch (\Throwable $e){
            return $this->writeJson(1,null,$e->getMessage());
        }
    }


}
