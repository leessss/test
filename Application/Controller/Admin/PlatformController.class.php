<?php

/**
 * 平台控制器
 */
class PlatformController extends Controller
{
    public function __construct(){
        //期望checkLogin方法验证成功 返回true 失败 返回false
        if ($this->checkLogin() === false) {
            $this->redirect("index.php?p=Admin&c=Login&a=login","没有登录，请登录！",3);
        }
    }
    /**
     * 验证登录信息
     * @return bool
     */
    private function checkLogin(){
        //判断是否存在该cookie  还要判断信息的值是否是 yes
//        @session_start();
        new SessionDBTool();
        if (!isset($_SESSION['USER_INFO'])) {
            //再来验证cookie中是否有信息，如果有将cookie中的密码和id取出来验证
            if (isset($_COOKIE['id']) && isset($_COOKIE['password'])) {
                //将cookie中的密码和id取出来验证
                $id = $_COOKIE['id'];
                $password = $_COOKIE['password'];
                $adminModel = new AdminModel();
                /**
                 * 期望该方法失败 false  成功 返回 用户信息的数组
                 */
                $result = $adminModel->checkByCookie($id,$password);
                if ($result !== false) {
                    //登录成功后将登录信息保存到session中
                    $_SESSION['USER_INFO'] = $result;
                    return true;
                }else{
                    return false;
                }

            }
            return false;
        }
    }
}