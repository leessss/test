<?php

/**
 * 专门执行sql的DB类
 */
class DB{
    private $host;//主机
    private $user;//用户
    private $password;//密码
    private $dbname;//数据库名
    private $port;//端口
    private $charset;//字符编码
    /**
     * 保存数据库链接
     * @var
     */
    private $link;

    /**
     * 保存单例对象
     * @var
     */
    private static $instance;
    /**
     * 初始化数据
     * DB constructor.
     * @param $host
     * @param $user
     * @param $password
     * @param $dbname
     * @param $port
     * @param $charset
     */
    private function __construct($config)
    {
        $this->host = isset($config['host']) ? $config['host'] : '127.0.0.1';
        $this->user = isset($config['user']) ? $config['user'] : 'root';
        $this->password = $config['password'];
        $this->dbname = $config['dbname'];
        $this->port = isset($config['port']) ? $config['port'] : 3306;
        $this->charset = isset($config['charset']) ? $config['charset'] : 'utf8';
        /**
         * 初始化链接数据和设置字符集
         */
        $this->connect();
        $this->setCharset();
    }

    /**
     * 公有的实例化对象方法
     *  语法：
     *  变量 instanceof 类名 ： 判断变量是否是类的一个实例化对象
     */
    public static function getInstance($config){
        if (!self::$instance instanceof self ) {//self表示本类
            self::$instance = new DB($config);
        }
        return self::$instance;
    }

    /**
     * 私有的构造方法
     */
    private function __clone(){

    }
    /**
     * 链接数据库
     */
    private function connect(){
        $this->link = mysqli_connect($this->host,$this->user,$this->password,$this->dbname,$this->port);
        if ($this->link === false) {//如果失败，打印错误信息
            die(
                '链接数据库失败！错误编号：'.mysqli_connect_errno()."<br/>".
                '错误信息:'.mysqli_connect_error()
            );
        }
    }
    /**
     * 设置字符集
     */
    private function setCharset(){
        $result = mysqli_set_charset($this->link,$this->charset);
        if ($result === false) {//设置字符编码失败
            die(
                '设置数据库编码失败！错误编号：'.mysqli_errno($this->link)."<br/>".
                '错误信息:'.mysqli_error($this->link)
            );
        }
    }

    /**
     * 执行sql语句
     * @param $sql 传入sql语句
     * @return bool|mysqli_result
     */
    public function query($sql){
        $result = mysqli_query($this->link,$sql);
        if ($result === false) {//执行sql失败
            die(
                '执行SQL失败！错误编号：'.mysqli_errno($this->link)."<br/>".
                '错误信息:'.mysqli_error($this->link)."<br/>".
                'SQL:'.$sql
            );
        }
        return $result;
    }

    /**
     * 执行sql语句返回一个二维数组
     * @param $sql
     */
    public function fetchAll($sql){
        //1.执行sql语句
        $result = $this->query($sql);
        //2.返回二维数组
        $rows = [];//准备一个新的数组，用于存放数据
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;//从结果集中取出数据并且放到新数组里面
        }
        return $rows;
    }

    /**
     * 执行sql，返回一行数据
     * @param $sql
     */
    public function fetchRow($sql){
        //1.执行sql
        $rows = $this->fetchAll($sql);
        //2.返回一行数据
        return empty($rows) ? null : $rows[0];
    }

    /**
     * 执行sql，返回第一行第一例的值
     * @param $sql
     */
    public function fetchColumn($sql){
        //1.执行sql
        $row = $this->fetchRow($sql);
        //2.返回第一行第一列的值
        return empty($row) ? null : array_values($row)[0];//返回关联数组的值
    }
    /**
     * 释放资源
     */
    public function __destruct(){
        //mysqli_close($this->link);
    }

    /**
     * 对象被序列化的时候调用
     * @return array
     */
    public function __sleep(){
        return ['host','user','password','dbname','port','charset'];
    }

    /**
     * 反序列化的时候调用
     */
    public function __wakeup(){
        $this->connect();
        $this->setCharset();
    }
    //获取最后生成的id
    public function last_insert_id(){
        return mysqli_insert_id($this->link);
    }
}
