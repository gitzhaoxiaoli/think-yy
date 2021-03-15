<?php

namespace app\index\model;

use Endroid\QrCode\QrCode;
use yy\MiniProgram;

/*
 * 二维码加背景
 * https: //blog.csdn.net/supramolecular/article/details/104280253
 */
class Poster
{
    public function __construct($imgFolder)
    {
        $this->imgFolder = $imgFolder;
    }

    private $imgFolder;

    public function create($data)
    {
        $posterName = $this->imgFolder . "/img/quotes_" . $data['id'] . time() . ".jpg";
//        if (file_exists($posterName))return str_replace('./' , '/' ,$posterName);
        // 1 获取画布尺寸
        $bg_w = 750;
        $bg_h = 1334;
        // 2 创建画图
        $img = @imagecreatetruecolor($bg_w, $bg_h);
        // 3 填充画布背景颜色
        $img_bg_color = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $img_bg_color);
        // 4 将主图填充到画布
        $data['photo'] = ROOT_PATH . $data['photo'];

        $photo = $this->getImgReource($data['photo']);
        list($goods_w, $goods_h) = getimagesize($data['photo']);
        $res = $this->calc($bg_w, 600 , $goods_w, $goods_h);

        imagecopyresampled($img, $photo, 0, 0,  $res[0] ,  $res[1] , $bg_w , 600 ,   $res[2] ,  $res[3] );

        // 5 填充饭店名称
        $font_color = ImageColorAllocate($img, 0, 0, 0); //字体颜色
        $font_ttf = realpath($this->imgFolder . "/font/msyhbd.ttc");
        imagettftext($img, 35, 0, 33, 700, $font_color, $font_ttf, $data['name']);
        // 6 填充饭店海报语录
        $font_ttf = realpath($this->imgFolder . "/font/msyhl.ttc");

        $title = mb_substr($data['quotes'] , 0 ,22);
        imagettftext($img, 22, 0, 40, 760, $font_color, $font_ttf, $title);

        if ($title = mb_substr($data['quotes'] , 22 ,22)){
            imagettftext($img, 22, 0, 40, 800, $font_color, $font_ttf, $title);
        }
        /*
                // 7 填充头像
                $data['avatarUrl'] = ROOT_PATH . $data['avatarUrl'];
                $avatarUrl = $this->getImgReource($data['avatarUrl']);
                list($user_w, $user_h) = getimagesize($data['avatarUrl']);
                imagecopyresized($img, $avatarUrl, 33, 1100, 0, 0, 90, 90, $user_w, $user_h);
                // 填充圆形
                $avatar = $this->getImgReource($this->imgFolder . '/bg/avatar.png');
                imagecopyresized($img, $avatar, 33, 1100, 0, 0, 90, 90,90, 90);
                // 8 填充昵称
                $font_ttf = realpath($this->imgFolder . "/font/msyhl.ttc");
                imagettftext($img, 20, 0, 156, 1127, $font_color, $font_ttf, $data['nickName']);
                // 9 填充时间
                $font_ttf = realpath($this->imgFolder . "/font/msyhl.ttc");
                imagettftext($img, 20, 0, 156, 1172, $font_color, $font_ttf, date('Y-m-d'));*/

        // 12 二维码
        $page = 'pages/user/hotelbar/hotelbar';
        $qecodeName = $this->generateQrcodeProgram($data['business_id'] ,$page , 'quotes');
        // halt($qecodeName);
        $qrcode = $this->getImgReource($qecodeName);
        // halt($qrcode);
        // 获取二维码尺寸
        list($qr_w, $qr_h) = getimagesize($qecodeName);
        imagecopyresized($img, $qrcode, 220, 860, 0, 0, 300, 300, $qr_w, $qr_h);

        $font_ttf = realpath($this->imgFolder . "/font/simhei.ttf");
        $label1 = "吃啥好";
        imagettftext($img, 45, 0, 140, 1270, $font_color, $font_ttf, $label1);
        $label2 = "扫一扫";
        imagettftext($img, 45, 0, 437, 1270, $font_color, $font_ttf, $label2);
        //  保存图片

        imagejpeg($img, $posterName);

//        unlink($qecodeName);
        return str_replace('./', '/', $posterName);

    }
    public function createLetter($data)
    {
        $posterName = $this->imgFolder . "/img/letter_" . $data['id'] . time() . ".jpg";
//        if (file_exists($posterName))return str_replace('./' , '/' ,$posterName);
        $data['photo'] = ROOT_PATH . $data['photo'];
        list($goods_w, $goods_h) = getimagesize($data['photo']);
        // 1 获取画布尺寸
        $bg_w = $goods_w;
        $bg_h = $goods_h;
        // 2 创建画图
        $img = @imagecreatetruecolor($bg_w, $bg_h);
        // 3 填充画布背景颜色
        // $img_bg_color = imagecolorallocate($img, 255, 255, 255);
        // imagefill($img, 0, 0, $img_bg_color);
        // 4 将主图填充到画布


        $photo = $this->getImgReource($data['photo']);

        // $res = $this->calc($bg_w, 864 , $goods_w, $goods_h);

        imagecopyresampled($img, $photo, 0, 0,  0 ,  0 , $bg_w , $bg_h , $bg_w , $bg_h  );

        // 5 填充饭店名称
        $font_color = ImageColorAllocate($img, 255, 255, 255); //字体颜色
        $font_ttf = realpath($this->imgFolder . "/font/msyhbd.ttc");
        imagettftext($img, 40, 0, 40, 900, $font_color, $font_ttf, $data['name']);
        // 6 填充饭店海报语录
        $font_ttf = realpath($this->imgFolder . "/font/msyhl.ttc");

        $title = mb_substr($data['quotes'] , 0 ,22);
        imagettftext($img, 22, 0, 40, 970, $font_color, $font_ttf, $title);

        if ($title = mb_substr($data['quotes'] , 22 ,22)){
            imagettftext($img, 22, 0, 40, 1010, $font_color, $font_ttf, $title);
        }
        // 时间 房间号 地址
        $data['date'] = "邀请时间：" . $data['date'];
        $data['room'] = "邀请房号：" . $data['room'];
        $data['address'] = "饭店地址：" . $data['address'];
        imagettftext($img, 22, 0, 40, 1050, $font_color, $font_ttf, $data['date']);
        imagettftext($img, 22, 0, 40, 1090, $font_color, $font_ttf, $data['room']);
        $address = mb_substr($data['address'] , 0 ,23);
        imagettftext($img, 22, 0, 40, 1130, $font_color, $font_ttf, $address);
        if ($address = mb_substr($data['address'] , 23 ,18)){
            imagettftext($img, 22, 0, 40, 1170, $font_color, $font_ttf, $address);
        }
        // 7 填充头像
        $data['avatarUrl'] = ROOT_PATH . $data['avatarUrl'];
        $avatarUrl = $this->getImgReource($data['avatarUrl']);
        list($user_w, $user_h) = getimagesize($data['avatarUrl']);
        imagecopyresized($img, $avatarUrl, 33, 1200, 0, 0, 90, 90, $user_w, $user_h);
        // 填充圆形
        // $avatar = $this->getImgReource($this->imgFolder . '/bg/avatar.png');
        // imagecopyresized($img, $avatar, 33, 1200, 0, 0, 90, 90,90, 90);
        // 8 填充昵称
        $font_ttf = realpath($this->imgFolder . "/font/msyhl.ttc");
        imagettftext($img, 20, 0, 156, 1227, $font_color, $font_ttf, $data['nickName']);
        // 9 填充时间
        $font_ttf = realpath($this->imgFolder . "/font/msyhl.ttc");
        imagettftext($img, 20, 0, 156, 1272, $font_color, $font_ttf, date('Y-m-d'));

        // 12 二维码
        $page = 'pages/user/hotelbar/hotelbar';
        $qecodeName = $this->generateQrcodeProgram($data['business_id'] ,$page , 'quotes');
        trace($qecodeName , '小程序');
        // halt($qecodeName);
        $qrcode = $this->getImgReource($qecodeName);
        // halt($qrcode);
        // 获取二维码尺寸
        list($qr_w, $qr_h) = getimagesize($qecodeName);
        imagecopyresized($img, $qrcode, 590, 1170, 0, 0, 150, 150, $qr_w, $qr_h);
        //  保存图片

        imagejpeg($img, $posterName);

//        unlink($qecodeName);
        return str_replace('./', '/', $posterName);

    }
    public function createByCode($data)
    {
        $posterName = $this->imgFolder . "/img/vote_" . $data['id'] . time() . ".jpg";
//        if (file_exists($posterName))return str_replace('./' , '/' ,$posterName);
        // 1 获取画布尺寸
        $bg_w = 750;
        $bg_h = 1334;
        // 2 创建画图
        $img = @imagecreatetruecolor($bg_w, $bg_h);
        // 3 填充画布背景颜色
        $img_bg_color = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $img_bg_color);
        // 4 将主图填充到画布
        $data['photo'] = ROOT_PATH . $data['photo'];

        $photo = $this->getImgReource($data['photo']);
        list($goods_w, $goods_h) = getimagesize($data['photo']);
        $res = $this->calc($bg_w, 750 , $goods_w, $goods_h);

        imagecopyresampled($img, $photo, 0, 0,  $res[0] ,  $res[1] , $bg_w , 750 ,   $res[2] ,  $res[3] );

        $font_color = ImageColorAllocate($img, 0, 0, 0); //字体颜色
        // 5 填充饭店海报语录
        $font_ttf = realpath($this->imgFolder . "/font/msyhl.ttc");

        $title = mb_substr($data['quotes'] , 0 ,22);
        imagettftext($img, 22, 0, 40, 820, $font_color, $font_ttf, $title);

        if ($title = mb_substr($data['quotes'] , 22 ,22)){

            imagettftext($img, 22, 0, 40, 860, $font_color, $font_ttf, $title);
        }
        // 6 填充饭店名称
        $font_ttf = realpath($this->imgFolder . "/font/msyhbd.ttc");
        imagettftext($img, 35, 0, 33, 930, $font_color, $font_ttf, '吃对了吗"厨艺比赛"');

        // 7 填充头像
        $data['avatarUrl'] = ROOT_PATH . $data['avatarUrl'];
        $avatarUrl = $this->getImgReource($data['avatarUrl']);
        list($user_w, $user_h) = getimagesize($data['avatarUrl']);
        imagecopyresized($img, $avatarUrl, 33, 990, 0, 0, 90, 90, $user_w, $user_h);
        // 填充圆形
        $avatar = $this->getImgReource($this->imgFolder . '/bg/avatar.png');
        imagecopyresized($img, $avatar, 33, 990, 0, 0, 90, 90,90, 90);
        // 8 填充昵称
        $font_ttf = realpath($this->imgFolder . "/font/msyhl.ttc");
        imagettftext($img, 20, 0, 156, 1020, $font_color, $font_ttf, $data['nickName']);
        // 9 填充编号
        $font_ttf = realpath($this->imgFolder . "/font/msyhl.ttc");
        imagettftext($img, 20, 0, 156, 1060, $font_color, $font_ttf, $data['number']);

        // 10 主打菜
        $font_ttf = realpath($this->imgFolder . "/font/msyhl.ttc");
        imagettftext($img, 20, 0, 35, 1151, $font_color, $font_ttf, '主打菜：'. $data['speciality']);

        // 11 创建时间
        $font_ttf = realpath($this->imgFolder . "/font/msyhl.ttc");
        imagettftext($img, 20, 0, 35, 1191, $font_color, $font_ttf, '创建时间：'. $data['create_time']);

        // 12 二维码
        $page = 'pages/user/match/detail/detail';
        $qecodeName = $this->generateQrcodeProgram($data['id'] ,$page , 'vote');
        $qrcode = $this->getImgReource($qecodeName);
        // halt($qrcode);
        // 获取二维码尺寸
        list($qr_w, $qr_h) = getimagesize($qecodeName);
        imagecopyresized($img, $qrcode, 490, 990, 0, 0, 200, 200, $qr_w, $qr_h);

        imagettftext($img, 20, 0, 156, 1280, $font_color, $font_ttf, '长按识别二维码为您支持的选手投票');



        //  保存图片

        imagejpeg($img, $posterName);

//        unlink($qecodeName);
        return str_replace('./', '/', $posterName);

    }


    public function createGoods($url,$bgFile , $goods, $user)
    {
        $posterName = $this->imgFolder . "/g" . $user['id'] . $goods['id'] . ".png";
//        if (file_exists($posterName))return str_replace('./' , '/' ,$posterName);
        // 1 获取背景图尺寸
        list($bg_w, $bg_h) = getimagesize($this->imgFolder . '/bg/' . $bgFile);
        // 2 创建画图
        $img = @imagecreatetruecolor($bg_w, $bg_h);
        // 3 填充画布背景颜色
        $img_bg_color = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $img_bg_color);
        // 4 将背景图填充到画布
        $bg_img = $this->getImgReource($this->imgFolder . '/bg/' . $bgFile);
        imagecopyresized($img, $bg_img, 0, 0, 0, 0, $bg_w, $bg_h, $bg_w, $bg_h);
        // 5 生成二维码， 填充用户二维码
        $qecodeName = $this->generateQrcode($url, $user['id']);
        $qrcode = $this->getImgReource($qecodeName);
        // 获取二维码尺寸
        list($qr_w, $qr_h) = getimagesize($qecodeName);
        imagecopyresized($img, $qrcode, 568, 866, 0, 0, 135, 135, $qr_w, $qr_h);
        // 填充商品主图
        $goods['main_pic'] = './' .$goods['main_pic'];
        $goodsImg = $this->getImgReource($goods['main_pic']);
        list($goods_w, $goods_h) = getimagesize($goods['main_pic']);
        $res = $this->calc($bg_w, 655 , $goods_w, $goods_h);

        imagecopyresampled($img, $goodsImg, 0, 0,  $res[0] ,  $res[1] , $bg_w , 655 ,   $res[2] ,  $res[3] );
        // 填充商品标题
        $font_color = ImageColorAllocate($img, 0, 0, 0); //字体颜色
        $font_ttf = realpath($this->imgFolder . "/font/msyhl.ttc");
        $title = mb_substr($goods['name'] , 0 ,18);
        imagettftext($img, 27, 0, 36, 717, $font_color, $font_ttf, $title);

        if ($title = mb_substr($goods['name'] , 18 ,18)){
            if (mb_strlen($title) > 18){
                $title = mb_substr($title , 0 ,17) . "...";
            }
            imagettftext($img, 27, 0, 36, 763, $font_color, $font_ttf, $title);
        }
        // 填充商品价格
        $font_color = ImageColorAllocate($img, 0, 0, 0); //字体颜色
        $font_ttf = realpath($this->imgFolder . "/font/msyhbd.ttc");
        $goods['price'] = "￥". number_format($goods['scpecs']['scpecsList'][0]['sale_price'] , 2);
        imagettftext($img, 30, 0, 46, 831, $font_color, $font_ttf, $goods['price']);
        // 填充昵称
        $font_color = ImageColorAllocate($img, 255, 0, 0); //字体颜色
        $font_ttf = realpath($this->imgFolder . "/font/msyhbd.ttc");
        imagettftext($img, 35, 0, 110, 910, $font_color, $font_ttf, $user['nickname']);
        // 8 保存图片

        imagepng($img, $posterName);

//        unlink($qecodeName);
        return str_replace('./', '/', $posterName);

    }

    public function generateQrcode($url, $user_id)
    {
        $qecodeName = $this->imgFolder . "/k" . $user_id . ".png";
        if (file_exists($qecodeName))return $qecodeName;
        $qrCode = new QrCode($url);
        $qrCode->setSize(400);
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(30);
        $qrCode->setEncoding('UTF-8');
//        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);

        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);
        $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);
        $qrCode->writeFile($qecodeName);
        return $qecodeName;
    }

    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /**
     * 获取图像文件资源
     * @param string $file
     * @return resource
     */
    protected function getImgReource($file)
    {
        $file_ext = pathinfo($file, PATHINFO_EXTENSION);
        switch ($file_ext) {
            case 'jpg':
            case 'jpeg':
                $img_reources = @imagecreatefromjpeg($file);
                break;
            case 'png':
                $img_reources = @imagecreatefrompng($file);
                break;
            case 'gif':
                $img_reources = @imagecreatefromgif($file);
                break;
        }
        return $img_reources;
    }

    /**
     * 通过画布宽高 原图像宽高 计算按画布尺寸缩放
     * @param $x 画布
     * @param $y 画布
     * @param $imgWidth 原图像
     * @param $imgHeight 原图像
     * @return array [裁剪位置x,y,图像宽高 x,y]
     */
    protected function calc($x , $y ,$imgWidth , $imgHeight)
    {
        //计算缩放因子
        $Des_scale = $imgWidth / $imgHeight; //原图像宽高比
        $Origin_scale = $x / $y; //画布宽高比
        if ($Des_scale>$Origin_scale) //画布宽高比小于画布宽高比，画布的宽度较大
        {
            $thumbh = $imgHeight;
            $thumbw = $imgHeight / $y * $x;
            //裁切位置 缩放后的宽减去布布宽/2 * 原图像宽高比  计算出原图像x开始点
            $desCutPos_x = ($imgWidth-$thumbw)/2;
            $desCutPos_y = 0;
        } else {
            $thumbw = $imgWidth;
            $thumbh = $imgWidth / $x * $y;
            $desCutPos_x = 0;
            $desCutPos_y = ($imgHeight-$thumbh)/2;
        }

        return [$desCutPos_x , $desCutPos_y, $thumbw , $thumbh ];
    }

    public function generateQrcodeProgram($id , $page , $type)
    {
        $qecodeName = $this->imgFolder . "/qrcode/" . md5($id . $page . $type) . ".jpg";
        if (file_exists($qecodeName))return $qecodeName;
        $AppID = $GLOBALS['config']['program']['appid'];
        $AppSecret = $GLOBALS['config']['program']['secret'];

        $m = new MiniProgram(compact('AppID' , 'AppSecret'));
        $token = $m->getAccessToken();

        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$token;

        $data = [
            // 'access_token' => $token,
            'scene' => $id,
            'page' => $page,
        ];
        trace($data , '小程序');
        // halt(json_encode($data));
        $result = httpRequest($url , 'POST' , json_encode($data));
        $temp = json_decode($result , true);
        if (!empty($temp['errcode']))return $temp;
        file_put_contents($qecodeName , $result);
        return $qecodeName;
    }


}

