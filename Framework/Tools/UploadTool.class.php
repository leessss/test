<?php

/**
 * 上传工具类
 */
class UploadTool
{
    //允许上传文件的大小
    private $max_size;
    //允许上传的类型
    private $allow_types;
    //保存错误信息
    private $error;
    /**
     * 初始数据
     * @param string $max_size
     * @param string $allow_types
     */
    public function __construct($max_size='',$allow_types=''){
        $this->max_size = !empty($max_size) ? $max_size : $GLOBALS['config']['upload']['max_size'];
        $this->allow_types = !empty($allow_types) ? $allow_types : $GLOBALS['config']['upload']['allow_types'];
    }

    /**
     * 获取错误信息
     */
    public function getError(){
        return $this->error;
    }
    /**
     * 上传一张图片
     * @param $fileinfo 图片信息
     * @param string $dir 图片路径
     */
    public function uploadOne($fileinfo,$dir = ''){
        //判断error 不等于 0 就是失败
        if ($fileinfo['error'] != 0) {
            $this->error = "上传失败!";
            return false;
        }
        //上传指定类型的图片文件
        if (!in_array($fileinfo['type'],$this->allow_types)){
            $this->error = "上传文件类型错误!";
            return false;
        }
        //检测文件大小 1024*1024*2
        if ($fileinfo['size'] > $this->max_size) {
            $this->error = "上传文件大小超过限制!";
            return false;
        }
        //判断文件是否是通过 HTTP POST 上传的
        if(!is_uploaded_file($fileinfo['tmp_name'])){
            $this->error = "不是上传的文件!";
            return false;
        }
        //处理文件名
        $filename = $dir.date("Ymd").'/'.uniqid().strrchr($fileinfo['name'],'.');
        //文件绝对路径
        $full_path = UPLOADS_PATH.$filename;

        //判断目录是否存在，如果不存在则创建目录
        $dirname = dirname($full_path);
        if (!is_dir($dirname)) {
            mkdir($dirname,0777,true);
        }
        //判断文件是否移动成功
        if(move_uploaded_file($fileinfo['tmp_name'],$full_path)){
            return $filename;//保存到数据库使用相对路径
        }else{
            $this->error = "移动文件失败！";
            return false;
        }
    }

    /**
     * 处理多张上传图片
     * @param $fileinfos 多张图片信息
     * @param string $dir 保存到位置
     */
    public function uploadMore($fileinfos,$dir=''){
        //重组多文件信息 表单名称相同
        $gallerys = [];//保存所有图片的路径
        foreach($fileinfos['error'] as $key=>$error){
            if ($error == 0){
                $fileinfo = [];//创建一个数组保存一个文件的所有信息
                $fileinfo['name'] = $fileinfos['name'][$key];
                $fileinfo['type'] = $fileinfos['type'][$key];
                $fileinfo['tmp_name'] = $fileinfos['tmp_name'][$key];
                $fileinfo['error'] = $fileinfos['error'][$key];
                $fileinfo['size'] = $fileinfos['size'][$key];
                $path = $this->uploadOne($fileinfo,$dir);
                if ($path !== false) {
                    $gallerys[] = $path;
                }else{
                    //只要一张商品图片上传失败就全部失败！
                    return false;
                }
            }
        }
        return $gallerys;
    }

    /**
     * 为一个不可访问的属性设置值
     * @param $name
     * @param $value
     */
    public function __set($name,$value){
        if (in_array($name,['max_size','allow_types'])) {
            $this->$name = $value;
        }
    }
    /**
     * 访问一个不可访问的属性
     * @param $name
     */
    public function __get($name){
        if($name == 'error'){
            return $this->$name;
        }
    }
}