<?php

namespace App\HttpController\App;


use App\Model\Medias;
use App\Model\Videos;

class IndexController extends AuthController
{


    public function index()
    {
        try {
            //判断用户VIP等级，返回对应等级的数据
            $uvip = 0;
            switch ($uvip){
                case 1:
                    $good = [
                        ['title' => '義理の母を躾けた僕 赤瀬尚子', 'link' => 'https://sycdn.comtucdncom.com/images/2022/05/26/kj21736.jpg'],
                        ['title' => 'N1207 生本番女子アナ初アナル貫通串刺カン', 'link' => 'https://sycdn.comtucdncom.com/images/2022/05/26/heyzo7608.jpg'],
                        ['title' => '留学生刘玥被猛男导师潜', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/5a1ba6ea26b4116d91e4142498d16a9c.jpg'],
                        ['title' => '有钱摄影大神酒店大尺度私拍', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/b2ec2affe99f873c7c395204e2f915a8.jpg'],
                        ['title' => '最新众筹网络红人K8傲娇萌萌', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/700b64a6b2b4deb62f97746a8c0e6628.jpg'],
                        ['title' => '强势的美艳御姐被炮机自慰棒', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/8f4c8ca6f716659b2a52d628fd2ed810.jpg'],
                        ['title' => '俄罗斯出差约操H罩杯极品', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/221690bd5bfc72aa78da38c0eca9ae11.jpg'],
                        ['title' => '钢琴前爆操巨乳学妹', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/1987c07a1d26546cf435bda92730249f.jpg'],
                        ['title' => '土豪五星級酒店约草36E巨乳', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/a2d35f333f9b231847c3bf4557ab03c4.jpg'],
                        ['title' => '混血爆乳女神李丽莎', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/854a4575dc5e7a8ecbde42b081c893e6.jpg'],
                        ['title' => '義理の母を躾けた僕 赤瀬尚子', 'link' => 'https://sycdn.comtucdncom.com/images/2022/05/26/kj21736.jpg'],
                        ['title' => 'N1207 生本番女子アナ初アナル貫通串刺カン', 'link' => 'https://sycdn.comtucdncom.com/images/2022/05/26/heyzo7608.jpg'],
                        ['title' => '留学生刘玥被猛男导师潜', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/5a1ba6ea26b4116d91e4142498d16a9c.jpg'],
                        ['title' => '有钱摄影大神酒店大尺度私拍', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/b2ec2affe99f873c7c395204e2f915a8.jpg'],
                        ['title' => '最新众筹网络红人K8傲娇萌萌', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/700b64a6b2b4deb62f97746a8c0e6628.jpg'],
                        ['title' => '强势的美艳御姐被炮机自慰棒', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/8f4c8ca6f716659b2a52d628fd2ed810.jpg'],
                        ['title' => '俄罗斯出差约操H罩杯极品', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/221690bd5bfc72aa78da38c0eca9ae11.jpg'],
                        ['title' => '钢琴前爆操巨乳学妹', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/1987c07a1d26546cf435bda92730249f.jpg'],
                        ['title' => '土豪五星級酒店约草36E巨乳', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/a2d35f333f9b231847c3bf4557ab03c4.jpg'],
                        ['title' => '混血爆乳女神李丽莎', 'link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/854a4575dc5e7a8ecbde42b081c893e6.jpg'],
                    ];
                    break;
                case 2:
                    $good = [
                        ['title' => '亲身体验深圳漂亮爆乳','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/153ea2b9a685cee1a006c2b7a8191ed9.jpg'],
                        ['title' => '假日带清纯巨乳女友到','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/cad3fabc15f954aa3f6bbaa94a085b5b.jpg'],
                        ['title' => '嫩模龙泽美曦私下兼','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/095ac35ed5c3d36acbde21a812727609.jpg'],
                        ['title' => '某银行经理和极品E奶情人','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/a7bd8e1f98a7e6e6ef2c729b6a18d8dd.jpg'],
                        ['title' => '桃色坏女友贪玩男友','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/e172ec9497d9ead7aa4a6b757919e542.jpg'],
                        ['title' => '公子哥同老铁驱车迎接刚下','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/a971ecf5aa6f5f9d2606fe00daff1423.jpg'],
                        ['title' => '淫乱师生恋性感家教诱惑学','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/a418d6f5d2adbf760a163912a2189126.jpg'],
                        ['title' => '空姐太寂寞想拍小视','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/f0202f6d614dc4240b855bbd464cd6c6.jpg'],
                        ['title' => '高顔值模特小騷逼被插出多','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/bbee1af23379b767a9caf17334105c25.jpg'],
                        ['title' => '國模宇航員系列大胸女神李梓熙','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/747e469bc061b3b98e0d5200f6c32b69.jpg'],
                        ['title' => '亲身体验深圳漂亮爆乳','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/153ea2b9a685cee1a006c2b7a8191ed9.jpg'],
                        ['title' => '假日带清纯巨乳女友到','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/cad3fabc15f954aa3f6bbaa94a085b5b.jpg'],
                        ['title' => '嫩模龙泽美曦私下兼','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/095ac35ed5c3d36acbde21a812727609.jpg'],
                        ['title' => '某银行经理和极品E奶情人','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/a7bd8e1f98a7e6e6ef2c729b6a18d8dd.jpg'],
                        ['title' => '桃色坏女友贪玩男友','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/e172ec9497d9ead7aa4a6b757919e542.jpg'],
                        ['title' => '公子哥同老铁驱车迎接刚下','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/a971ecf5aa6f5f9d2606fe00daff1423.jpg'],
                        ['title' => '淫乱师生恋性感家教诱惑学','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/a418d6f5d2adbf760a163912a2189126.jpg'],
                        ['title' => '空姐太寂寞想拍小视','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/f0202f6d614dc4240b855bbd464cd6c6.jpg'],
                        ['title' => '高顔值模特小騷逼被插出多','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/bbee1af23379b767a9caf17334105c25.jpg'],
                        ['title' => '國模宇航員系列大胸女神李梓熙','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/747e469bc061b3b98e0d5200f6c32b69.jpg'],
                    ];
                    break;
                case 3:
                    $good = [];
                    break;
                case 4:
                    $good = [];
                    break;
                case 5:
                    $good = [];
                    break;
                case 6:
                    $good = [];
                    break;
                case 7:
                    $good = [];
                    break;
                case 8:
                    $good = [];
                    break;
                case 9:
                    $good = [];
                    break;
                default:
                    $good = [
                        ['title' => '亲身体验深圳漂亮爆乳','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/153ea2b9a685cee1a006c2b7a8191ed9.jpg'],
                        ['title' => '假日带清纯巨乳女友到','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/cad3fabc15f954aa3f6bbaa94a085b5b.jpg'],
                        ['title' => '嫩模龙泽美曦私下兼','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/095ac35ed5c3d36acbde21a812727609.jpg'],
                        ['title' => '某银行经理和极品E奶情人','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/a7bd8e1f98a7e6e6ef2c729b6a18d8dd.jpg'],
                        ['title' => '桃色坏女友贪玩男友','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/e172ec9497d9ead7aa4a6b757919e542.jpg'],
                        ['title' => '公子哥同老铁驱车迎接刚下','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/a971ecf5aa6f5f9d2606fe00daff1423.jpg'],
                        ['title' => '淫乱师生恋性感家教诱惑学','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/a418d6f5d2adbf760a163912a2189126.jpg'],
                        ['title' => '空姐太寂寞想拍小视','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/f0202f6d614dc4240b855bbd464cd6c6.jpg'],
                        ['title' => '高顔值模特小騷逼被插出多','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/bbee1af23379b767a9caf17334105c25.jpg'],
                        ['title' => '國模宇航員系列大胸女神李梓熙','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/747e469bc061b3b98e0d5200f6c32b69.jpg'],
                        ['title' => '亲身体验深圳漂亮爆乳','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/153ea2b9a685cee1a006c2b7a8191ed9.jpg'],
                        ['title' => '假日带清纯巨乳女友到','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/cad3fabc15f954aa3f6bbaa94a085b5b.jpg'],
                        ['title' => '嫩模龙泽美曦私下兼','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/095ac35ed5c3d36acbde21a812727609.jpg'],
                        ['title' => '某银行经理和极品E奶情人','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/a7bd8e1f98a7e6e6ef2c729b6a18d8dd.jpg'],
                        ['title' => '桃色坏女友贪玩男友','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/e172ec9497d9ead7aa4a6b757919e542.jpg'],
                        ['title' => '公子哥同老铁驱车迎接刚下','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/a971ecf5aa6f5f9d2606fe00daff1423.jpg'],
                        ['title' => '淫乱师生恋性感家教诱惑学','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/a418d6f5d2adbf760a163912a2189126.jpg'],
                        ['title' => '空姐太寂寞想拍小视','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/f0202f6d614dc4240b855bbd464cd6c6.jpg'],
                        ['title' => '高顔值模特小騷逼被插出多','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/bbee1af23379b767a9caf17334105c25.jpg'],
                        ['title' => '國模宇航員系列大胸女神李梓熙','link' => 'https://sycdn.comtucdncom.com/upload/vod/20210923-1/747e469bc061b3b98e0d5200f6c32b69.jpg'],
                    ];
                    break;
            }

            return $this->writeJson(0, encrypt_data($good));
        } catch (\Throwable $e) {
            write_log('Index-index:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }

    }


    public function check(){
        try {
            $data = $this->params();
            $version = $data['version'] ?? '';
            $link = setting('update_url');
            $_version = setting('app_version');
            if($_version != $version){
                return $this->writeJson(0,encrypt_data($link));
            }else{
                return $this->writeJson(1, null, '版本一致');
            }
        } catch (\Throwable $e) {
            write_log('Index-check:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }


}
