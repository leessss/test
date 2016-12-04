<?php

/**
 * session入库
 */
class SessionDBTool
{
    //保存db对象
    private $db;

    public function __construct(){
        session_write_close();//强制关闭session
        session_set_save_handler(
            array($this,'open'),
            array($this,'close'),
            array($this,'read'),
            array($this,'write'),
            array($this,'destroy'),
            array($this,'gc')
        );
        @session_start();//创建对象的时候就帮我们开启session
    }
    /**
     * 用于初始化一数据
     * @param $savePath
     * @param $sessionName
     */
    public function open($savePath, $sessionName){
        $this->db = DB::getInstance($GLOBALS['config']['db']);
        return true;
    }
    /**
     * 释放资源
     * 在 write 回调函数调用之后调用
     */
    public function close(){
        return true;
    }
    /**
     * 如果会话中有数据，read 回调函数必须返回序列化后的字符串。
     * 如果会话中没有数据，read 回调函数返回空字符串。
     *
     * 读取出来后php自己将 它反序列化后保存到 $_SESSION
     * @param $sessionId
     */
    public function read($sessionId){
        $sql = "select sess_data from session WHERE sess_id='{$sessionId}' limit 1";
        $sess_data = $this->db->fetchColumn($sql);
        return empty($sess_data) ? '' : $sess_data;
    }
    /**
     * 在会话保存数据时会调用 write 回调函数。
     * 此回调函数接收当前会话 ID 以及 $_SESSION 中数据序列化之后的字符串作为参数。
     * @param $sessionId
     * @param $data 序列化后的字符串
     */
    public function write($sessionId, $data){
        $sql = "insert into session VALUES ('{$sessionId}','{$data}',unix_timestamp()) ON DUPLICATE KEY UPDATE sess_data='{$data}',last_modified = unix_timestamp()";
        $this->db->query($sql);
    }
    /**
     * 当调用 session_destroy() 函数
     * 会调用此回调函数。此回调函数操作成功返回 TRUE，反之返回 FALSE。
     * 当调用 session_destroy() 方法后 write方法不执行
     * @param $sessionId
     */
    public function destroy($sessionId){
        $sql = "delete from session WHERE sess_id='{$sessionId}'";
        $this->db->query($sql);
        return true;
    }
    /**
     * 垃圾回收
     * 此回调函数操作成功返回 TRUE，反之返回 FALSE。
     * @param $lifetime
     */
    public function gc($lifetime){
        $sql = "delete from session where last_modified + {$lifetime} < unix_timestamp()";
        $this->db->query($sql);
        return true;
    }
}