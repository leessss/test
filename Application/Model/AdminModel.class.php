<?php

/**
 * 管理员模型类文件
 */
class AdminModel extends Model
{
    public function add($data){
        //a.用户名不能为空
        if (empty($data['username'])) {
            $this->error = "用户名不能为空！";
            return false;
        }
        //b.email不能为空
        if (empty($data['email'])) {
            $this->error = "邮箱不能为空！";
            return false;
        }
        //c.密码不能为空
        if (empty($data['password'])) {
            $this->error = "密码不能为空！";
            return false;
        }
        //d.密码和确认密码必须一致
        if($data['password'] != $data['repassword']){
            $this->error = "密码不一致，请重写输入！";
            return false;
        }
        $data['password'] = md5($data['password']);
        $data['add_time'] = time();//获取当前时间戳
        //调用父类中的方法完成添加
        parent::insertData($data);
    }

    /**
     * 重写更新方法
     */
    public function updateData($new_data){
        //a.用户名不能为空
        if (empty($new_data['username'])) {
            $this->error = "用户名不能为空！";
            return false;
        }
        //b.email不能为空
        if (empty($new_data['email'])) {
            $this->error = "邮箱不能为空！";
            return false;
        }
        if (empty($new_data['old_password'])) {
            //如果没有填写旧密码就不修改密码
            unset($new_data['password']);//删除$new_data中的password字段
        }else{
            //如果填写了旧密码就需要修改密码
            //1.旧密码不能为空
            if(empty($new_data['old_password'])){
                $this->error = "旧密码必须填写！";
                return false;
            }
            //2.新密不能为空
            if (empty($new_data['password'])){
                $this->error = "新密码不能为空！";
                return false;
            }
            //3.新密码和新确认密码必须一致
            if ($new_data['password'] != $new_data['repassword']){
                $this->error = "新密码和确认密码不一致！";
                return false;
            }
            //4.判断旧密码与数据库中的密码一致
            //a.获取数据库中的旧密码
            $password_in_db = $this->getColumn("password","id={$new_data['id']}");
            //b.将传过来旧密码MD5加密后在与数据库中的密码进行比对
            $old_password = md5($new_data['old_password']);
            if ($password_in_db != $old_password) {
                $this->error = "旧密码输入错误！";
                return false;
            }
            //将传入的新密码进行md5加密
            $new_data['password'] = md5($new_data['password']);
        }
        parent::updateData($new_data);
    }

    /**
     * 验证数据库中的用户名和密码
     * @param $username
     * @param $password
     */
    public function check($username,$password){
        //将传过来的密码加密
        $password = md5($password);
        //准备 sql
//        $sql = "select * from admin WHERE username='{$username}' and password='{$password}' limit 1";
//        $row = $this->db->fetchRow($sql);
        $row = $this->getRow("username='{$username}' and password='{$password}'");
        if (empty($row)){
            $this->error = "用户名或者密码错误，请重新输入！";
            return false;
        }else{
            return $row;
        }
    }

    /**
     * 验证用户id和密码
     * @param $id
     * @param $password md5($password."itsource")
     */
    public function checkByCookie($id,$password){
        //准sql  是根据id取出用户信息
//        $sql = "select * from admin WHERE id={$id} limit 1";
//        $row = $this->db->fetchRow($sql);
        $row = $this->getByPk($id);
        //将用户信息中的密码取出来与 itsource 拼接后再加密
        $password_in_db = md5($row['password']."itsource");
        //比对传入的密码和获取的密码是否一致
        if ($password_in_db == $password) {
            return $row;
        }else{
            return false;
        }
    }

    /**
     * 修改登录用户
     * @param $data
     */
    public function update($data){
        if(empty($data['username'])){
            $this->error = "用户名不能为空！";
            return false;
        }
        if(empty($data['email'])){
            $this->error = "邮箱不能为空！";
            return false;
        }
        //获取用户id
        $id = $_SESSION['USER_INFO']['id'];
        if (empty($data['old_password'])) {//没有传旧密码
            unset($data['password']);
//            $sql = "update admin set username='{$data['username']}',email='{$data['email']}' WHERE id={$id}";
        }else{
            if (empty($data['password'])) {
                $this->error = "密码不能为空！";
                return false;
            }
            if ($data['password'] != $data['repassword']) {
                $this->error = "密码和确认密码不相同！";
                return false;
            }

//            $sql = "select password from admin WHERE id={$id} limit 1";
//            $password_in_db = $this->db->fetchColumn($sql);
            $password_in_db = parent::getColumn("password","id={$id}");
            if (md5($data['old_password']) != $password_in_db) {
                $this->error = "旧密码输入错误！";
                return false;
            }
            //加密密码
            $data['password'] = md5($data['password']);
//            $sql = "update admin set username='{$data['username']}',email='{$data['email']}',password=md5('{$data['password']}') WHERE id={$id}";
        }
        //将用户id保存到data
        $data['id'] = $id;
        return parent::updateData($data);
    }
}