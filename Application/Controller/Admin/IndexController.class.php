<?php

/**
 * 后台首页
 */
class IndexController extends PlatformController
{
    public function index(){
        //1.接收请求参数
        //2.处理数据
        //3.显示页面
        $this->display("index");
    }
    public function top(){
        //1.接收请求参数
        @session_start();
        $username = $_SESSION['USER_INFO']['username'];
        //2.处理数据

        //3.显示页面
        $this->assign("username",$username);
        $this->display("top");
    }

    public function menu(){
        //1.接收请求参数
        //2.处理数据
        //3.显示页面
        $this->display("menu");
    }

    public function main(){
        //1.接收请求参数
        //2.处理数据
        //3.显示页面
        $this->display("main");
    }
}