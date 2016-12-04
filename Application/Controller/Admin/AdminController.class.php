<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/19 0019
 * Time: 上午 11:24
 */
class AdminController extends PlatformController
{
    public function index(){
        //1，接收数据
        //2，处理数据
        $adminModel = new AdminModel();
        $rows = $adminModel->getAll();
        //3，显示页面
        $this->assign('rows',$rows);
        $this->display("index");
    }

    /**
     * 同时完成管理员的添加展示和添加功能
     */
    public function add(){
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            //1，接收数据
            $data = $_POST;
            //2，处理数据
            $adminModel = new AdminModel();
            $result = $adminModel->add($data);
            if ($result === false) {//如果添加失败提示错误信息
                $this->redirect('index.php?p=Admin&c=Admin&a=add',"添加失败！".$adminModel->getError(),3);
            }else{
                //3，显示页面
                $this->redirect('index.php?p=Admin&c=Admin&a=index');
            }
        }else{
            //1，接收数据
            //2，处理数据
            //3，显示页面
            $this->display('add');
        }

    }
    public function delete(){
        //1，接收数据
        $id = $_GET['id'];
        //2，处理数据
        $adminModel = new AdminModel();
        $adminModel->deleteByPk($id);
        //3，显示页面
        $this->redirect('index.php?p=Admin&c=Admin&a=index');
    }

    /**
     * 同时完成管理员修改的展示和更新工作
     */
    public function edit(){
        if ($_SERVER['REQUEST_METHOD'] == "POST"){
            //1，接收数据
            $data = $_POST;
            //2，处理数据
            $adminModel = new AdminModel();
            $result = $adminModel->updateData($data);//如果填写不符合要求返回false，并且保存错误信息
            if ($result === false) {
                $this->redirect("index.php?p=Admin&c=Admin&a=edit&id=".$data['id'],'更新失败！'.$adminModel->getError(),2);
            }
            //3，显示页面
            $this->redirect('index.php?p=Admin&c=Admin&a=index');
        }else{
            //1，接收数据
            $id = $_GET['id'];
            //2，处理数据
            $adminModel = new AdminModel();
            $row = $adminModel->getByPk($id);
            //3，显示页面
            $this->assign($row);
            $this->display('edit');
        }
    }

    public function editSelf(){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = $_POST;
            $adminModel = new AdminModel();
            $result = $adminModel->update($data);
            if ($result === false) {
                $this->redirect("index.php?p=Admin&c=Admin&a=editSelf",$adminModel->getError(),3);
            }else{
                $loginController = new LoginController();
                $loginController->logout();
            }
        }else{
            $this->assign($_SESSION['USER_INFO']);
            $this->display("editSelf");
        }
    }
}