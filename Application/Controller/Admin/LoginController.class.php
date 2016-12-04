<?php

/**
 * 登录控制器
 */
class LoginController extends Controller
{
    public function login(){
        //1.接收请求参数
        //2.处理数据
        //3.显示页面
        $this->display("login");
    }

    /**
     * 验证登录
     */
    public function check(){
        //开启session
        new SessionDBTool();
        //验证验证码
        if (!CaptchaTool::check($_POST['captcha'])) {
            $this->redirect("index.php?p=Admin&c=Login&a=login",'验证码输入错误，请重写输入！',3);
        }

        //1.接收请求参数
        $username = $_POST['username'];
        $password = $_POST['password'];
        //2.处理数据
        $adminModel = new AdminModel();
        /**
         * 用于验证输入的用户名和密码是否正确
         * 如果验证 失败 返回 false 成功 返回当前用户信息的数组
         */
        $result = $adminModel->check($username,$password);
        if ($result !== false) {//验证成功跳转到首页
            //将登录信息保存到cookie中
//            setcookie("isLogin","yes");
            //将登录信息保存到session中
//            $_SESSION['isLogin'] = "yes";
            //将用户信息保存到session中

            $_SESSION["USER_INFO"] = $result;

            //如果传了记住登录的标识  remember 才记录
            if (isset($_POST['remember'])) {
                //获取用户id和密码
                $id = $result['id'];
                $password = md5($result['password']."itsource");//密码取出来后再拼接一个字符串之后在MD5加密
                setcookie("id",$id,time()+60*60*24*7,'/');//保存id
                setcookie("password",$password,time()+60*60*24*7,'/');//保存密码
            }
            $this->redirect("index.php?p=Admin&c=Index&a=index");
        }else{
            $this->redirect("index.php?p=Admin&c=Login&a=login","登录失败！".$adminModel->getError(),3);
        }
        //3.显示页面
    }
    //退出 注销
    public function logout(){
        //1，接收数据
        //2，处理数据
            //删除cookie中的数据
            setcookie("id",null,-1,'/');//注意一定要加上目录 /
            setcookie("password",null,-1,'/');//注意一定要加上目录 /
            //删除session
//            session_start();
            new SessionDBTool();
            unset($_SESSION["USER_INFO"]);//只能删除用户信息
        //3，显示页面
        $this->redirect("index.php?p=Admin&c=Login&a=login");
    }
}