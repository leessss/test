<?php

/**
 * 图片处理工具
 */
class ImageTool
{
    private $error;

    public function getError(){
        return $this->error;
    }
    //创建图片的方法映射
    private $createFunc = [
        'image/png'=>'imagecreatefrompng',
        'image/jpeg'=>'imagecreatefromjpeg',
        'image/gif'=>'imagecreatefromgif'
    ];
    //输出图片的方法映射
    private $outFunc = [
        'image/png'=>'imagepng',
        'image/jpeg'=>'imagejpeg',
        'image/gif'=>'imagegif'
    ];
    /**
     * 生成指定大小的缩略图
     * @param $src_path 原图路径
     * @param $thumb_width 目标图片宽度
     * @param $thumb_height 目标图片高度
     * @param $type 图片处理 类型 0 补白
     */
    public function thumb($src_path,$thumb_width,$thumb_height,$type = 0){
        //1.准备原图
        $src_path = UPLOADS_PATH.$src_path;//绝对路径
        if(!is_file($src_path)){//判断原图是否存在
            $this->error = "原图片不存在！";
            return false;
        }
        $imagesize = getimagesize($src_path);
        //获取图片的宽高
        list($src_width,$src_height) = $imagesize;
        //获取图片的mime类型
        $mime = $imagesize['mime'];
        //获取创建方法
        $createFunc = $this->createFunc[$mime];
        $src_img = $createFunc($src_path);//可变方法名
//        if ($mime == 'image/png') {
//            $src_img = imagecreatefrompng($src_path);
//        }elseif($mime == 'image/jpeg'){
//            $src_img = imagecreatefromjpeg($src_path);
//        }


        //2.准备目标图片
        $thumb_img = imagecreatetruecolor($thumb_width,$thumb_height);
        //补白
        switch($type){
            case 0:
                $white = imagecolorallocate($thumb_img,255,255,255);
                imagefill($thumb_img,0,0,$white);
                break;
        }

        //等比例缩放
        //计算出最大缩放比例
        $scale = max($src_width/$thumb_width,$src_height/$thumb_height);
        //计算缩放后的宽度和高度
        $width = $src_width/$scale;
        $height = $src_height/$scale;

        /**
         * 3。将原图拷贝目标图片
         *  imagecopyresampled (
         * resource $dst_image , ---- 目标图片（资源）
         * resource $src_image , ---- 原图（资源）
         * int $dst_x , int $dst_y , --- 目标坐标起始点（从目标的什么位置开始画图）
         * int $src_x , int $src_y , --- 原图坐标起始点
         * int $dst_w , int $dst_h , --- 目标坐标结束点
         * int $src_w , int $src_h   --- 原图坐标结束点
         * )

         */
        imagecopyresampled($thumb_img,$src_img,($thumb_width-$width)/2,($thumb_height-$height)/2,0,0,$width,$height,$src_width,$src_height);

        //获取目标图片路径
        //D:\server\apache\htdocs\day13\shop_v36\Uploads\goods\20161128\583b89c61ff4e.jpg
        //D:\server\apache\htdocs\day13\shop_v36\Uploads\goods\20161128\583b89c61ff4e_60x60.jpg
        $pathinfo = pathinfo($src_path);
        $thumb_path = $pathinfo['dirname'].'/'.$pathinfo['filename']."_{$thumb_width}x{$thumb_height}.".$pathinfo['extension'];
        //4.输出图片
        //获取输出方法
        $outFunc = $this->outFunc[$mime];
        $outFunc($thumb_img,$thumb_path);
//        if ($mime == "image/png") {
//            imagepng($thumb_img,$thumb_path);
//        }elseif($mime == "image/jpeg"){
//            imagejpeg($thumb_img,$thumb_path);
//        }

        //5.销毁图片
        imagedestroy($thumb_img);
        imagedestroy($src_img);
        //返回缩略图路径
        return str_replace(UPLOADS_PATH,'',$thumb_path);
    }
}