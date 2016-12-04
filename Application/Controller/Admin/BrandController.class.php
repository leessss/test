<?php

/**
 */
class BrandController extends PlatformController
{
    public function index(){
        //接收请求数据
        //处理数据
        $brandModel = new BrandModel();
        $rows = $brandModel->getAll("name='小米1'");
        //显示页面
        $this->assign('rows',$rows);
        $this->display("index");
    }

    public function remove(){
        //接收请求数据
        $id = $_GET['id'];
        //处理数据
        $brandModel = new BrandModel();
        $brandModel->remove($id);
        //显示页面
//        flush();
        $this->redirect("index.php?p=Admin&c=Brand&a=index");
//        $this->redirect("index.php?p=Admin&c=Brand&a=index",'错误信息！',3);
    }

    public function add(){
        //1.接收数据
            $data = ['id'=>2,'name'=>'小米','url'=>'wwww.xiaomi.com','logo'=>'','intro'=>'为发烧而生！！！！','xxx'=>'xxx'];
        //2.处理数据
            $brandModel = new BrandModel();
            $count = $brandModel->getColumn('url',"name='小米'");
            var_dump($count);
        //3.显示页面

    }
}