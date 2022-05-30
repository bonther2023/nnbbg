<?php

namespace App\Utility;

class FtpTool
{

    protected $options;
    protected $connect; // FTP连接

    public function __construct()
    {
        $options = config('FTP');
        $this->options = $options;
        $this->connect = ftp_connect($this->options['host'], $this->options['port']);
        if (!$this->connect) {
            throw new \Exception("不能链接到服务器： $this->options['host']");
        }
        $login = ftp_login($this->connect, $this->options['user'], $this->options['auth']);
        if (!$login) {
            throw new \Exception("登录FTP服务器失败");
        }
        //打开被动模拟，只能在成功登录后调用,否则将失败.
        ftp_pasv($this->connect, true);
    }


    /**
     * 方法：上传文件
     * @path -- 本地路径
     * @newpath -- 上传路径
     * @filename -- 文件名
     */
    public function upload($path, $newpath, $filename)
    {
        //改变 FTP 服务器上的当前目录。
        if(ftp_chdir($this->connect, $newpath) == FALSE){
            $dir = ftp_mkdir($this->connect, $newpath);
            if (!$dir) {
                throw new \Exception("目录创建失败，请检查权限及路径是否正确！");
            }
        }
        //把当前目录改变为 FTP 服务器上的父目录。
        ftp_cdup($this->connect);
        $result = ftp_put($this->connect, $filename, $path);//, FTP_ASCII被动模式传的图片失真了
        if (!$result) {
            throw new \Exception("文件上传失败");
        }
        $this->close();
    }

    /**
     * 方法：关闭FTP连接
     */
    protected function close()
    {
        ftp_close($this->connect);
    }


}
