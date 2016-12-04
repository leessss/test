<?php

/**
 * 基础控制器类
 */
abstract class Controller
{
    private $data = [];//保存所有需要分配到页面的数据
    /**
     * 选择视图
     * @param string $template 模板名称
     */
    protected function display($template=''){
        /**
         * 一个关联数组。此函数会将键名当作变量名，值作为变量的值。
         * 对每个键／值对都会在当前的符号表中建立变量，
         */
        extract($this->data);
        require CURRENT_VIEW_PATH.$template.'.html';
        exit;
    }

    /**
     * 将数据分配到视图页面，页面通过key获取数据
     * @param $key
     * @param $value
     */
    protected function assign($key,$value=''){
        if (is_array($key)) {
            $this->data = array_merge($this->data,$key);//合并数组
        }else{
            $this->data[$key] = $value;
        }
    }

    /**
     * @param 跳转的url $url
     * @param string $msg 提示信息
     * @param int $times 等待时间  单位 秒
     */
    protected function redirect($url,$msg='',$times=0){
        //headers_sent() 如果发送 返回true 没有 false
        if(headers_sent()){
            if ($times){
                echo "<h1>{$msg}</h1>";
                $times = $times*1000;
            }
            echo <<<JS
            <script type="text/javascript">
                window.setTimeout(function(){
                    location.href = "{$url}";
                },{$times});
            </script>
JS;
        }else{
            if ($times) {//提示信息后跳转
                echo "<h1>{$msg}</h1>";
                header("Refresh: {$times};{$url}");
            }else{//直接跳转
                header("Location:{$url}");
            }
        }
        //必须退出
        exit;
    }
}