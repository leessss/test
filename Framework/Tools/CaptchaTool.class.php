<?php

/**
 * 验证码工具类
 */
class CaptchaTool
{
    /**
     * 生成指定长度的随机码
     * @param $num
     */
    private static function makeCode($num){
        //1．随机码值
        $chars = "23456789ABCDEFGHJKNMPQRSTUVWXYZ";//准备数组
        $chars = str_shuffle($chars);//打乱字符串
        return substr($chars,0,$num);//生成随机字符串
    }
    /**
     * 生成指定长度的验证码
     * @param int $num
     */
    public static function generate($num = 6){
        //1．随机码值
        $random_code = self::makeCode($num);
        //将随机码保存到session中
        new SessionDBTool();
        $_SESSION['random_code'] = $random_code;

        //2．随机背景
        $image_path = TOOLS_PATH."captcha/captcha_bg".mt_rand(1,5).".jpg";
        list($width,$height) = getimagesize($image_path);

        $image = imagecreatefromjpeg($image_path);
        //3．红色边框
        $gray = imagecolorallocate($image,178, 0, 7);
        imagerectangle($image,0,0,$width-1,$height-1,$gray);

        //混淆
            //画点
//            for($i = 0 ; $i < 100 ; ++$i){
//                $color = imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
//                imagesetpixel($image,mt_rand(1,$width-1),mt_rand(1,$height-1),$color);
//            }
            //画线
//            for($i = 0 ; $i < 10 ; ++$i){
//                $color = imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
//                imageline($image,mt_rand(1,$width-1),mt_rand(1,$height-1),mt_rand(1,$width-1),mt_rand(1,$height-1),$color);
//            }

        //4．字体随机白色黑色
        $white = imagecolorallocate($image,255, 255, 255);
        $black = imagecolorallocate($image,0, 0, 0);
        imagestring($image,5,$width/3,$height/8,$random_code,mt_rand(0,1) ? $white : $black);
        //4.输出图片
        header("Content-Type: image/jpeg;charset=utf-8");
        imagejpeg($image);
        //5.销毁图片
        imagedestroy($image);
    }

    /**
     * 验证验证码
     * @param $captcha
     */
    public static function check($captcha){
        //将session中保存的随机码取出来
        new SessionDBTool();
        $random_code = $_SESSION['random_code'];
        return strtolower($random_code) == strtolower($captcha);
    }
}