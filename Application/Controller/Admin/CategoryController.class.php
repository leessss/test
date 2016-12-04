<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17 0017
 * Time: 上午 11:49
 */
class CategoryController extends Controller
{
    public function index()
    {
        //1.接收请求数据
        //2.处理数据
        $categoryModel = new CategoryModel();
        $rows = $categoryModel->getList();
        //3.显示页面
        $this->assign("rows",$rows);
        $this->display("index");
    }

    /**
     *商品分类的添加展示和添加功能
     *
     */
    public function add(){
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            //1.接收请求数据
            $data = $_POST;
            //2.处理数据
            $categoryModel = new CategoryModel();
            $result = $categoryModel->insertData($data);//如果不满足需求返回false
            if ($result === false) {
                $this->redirect("index.php?p=Admin&c=Category&a=add","添加失败！".$categoryModel->getError(),3);
            }else{
                //3.显示页面
                $this->redirect("index.php?p=Admin&c=Category&a=index");
            }
        }else{
            //1.接收请求数据
            //2.处理数据
            $categoryModel = new CategoryModel();
            $categorys = $categoryModel->getList();
            //3.显示页面
            $this->assign("category",$categorys);
            $this->display("add");
        }
    }

    /**
     * 展示修改和 保存修改
     */
    public function edit(){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            //1.接收请求数据
            $data = $_POST;
            //2.处理数据
            //期望模型上有一个方法可以根据id更新数据
            $categoryModel = new CategoryModel();
            $result = $categoryModel->updateData($data);//如果不满足需求false
            if ($result === false) {
                $this->redirect("index.php?p=Admin&c=Category&a=edit&id=".$data['id'],"修改失败！".$categoryModel->getError(),3);
            }else{
                //3.显示页面
                $this->redirect("index.php?p=Admin&c=Category&a=index");
            }
        }else{
            //1.接收请求数据
            $id = $_GET['id'];
            //2.处理数据
            //期望模型上有一个方法可以根据id查询出一条数据
            $categoryModel = new CategoryModel();
            $row = $categoryModel->getByPk($id);
            //查询出所有分类数据（排序好和缩进好的）
            $category = $categoryModel->getList();
            //3.显示页面
            $this->assign($row);//分配当前id对应的分类数据
            $this->assign("category",$category);//分配所有分类数据
            $this->display('edit');
        }
    }

    /**
     * 删除分类
     */
    public function delete(){
        //1.接收请求数据
        $id = $_GET['id'];
        //2.处理数据
        $categoryModel = new CategoryModel();
        $result = $categoryModel->delete($id);//如果删除失败，返回false并且提示错误信息
        if ($result === false) {
            $this->redirect("index.php?p=Admin&c=Category&a=index","删除失败！".$categoryModel->getError(),3);
        }
        //3.显示页面
        $this->redirect("index.php?p=Admin&c=Category&a=index");
    }
}