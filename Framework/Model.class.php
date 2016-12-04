<?php

/**
 * 基础模型类
 */
abstract class Model
{
    //表名 供子类重写指定表名
    protected $table_name;
    //保存db对象 被子类继承所以用protected 不用private
    protected $db;
    //保存错误信息
    protected $error;

    //存表的所有字段
    private $fields = [];
    public function __construct(){
        $this->db = DB::getInstance($GLOBALS['config']['db']);
        //初始化表的字段
        $this->initFields();
    }
    /**
     * 获取错误信息
     */
    public function getError(){
        return $this->error;
    }

    /**
     * 获取当前表的所有字段
     *
     * $fields = ['pk'=>'id','name','url','logo','intro'];
     */
    private function initFields(){
        $sql = "desc {$this->table()}";
        $rows = $this->db->fetchAll($sql);
        foreach($rows as $row){
            if ($row['Key'] == "PRI") {
                $this->fields['pk'] = $row['Field'];
            }else{
                $this->fields[] = $row["Field"];
            }
        }
    }
    /**
     * 处理表名 获取真是表名
     * 返回处理后的表名
     */
    private function table(){
        if (empty($this->table_name)){
            //1.获取类名
            $class_name = get_class($this);
            //2.截取表名
            //strpos — 查找字符串首次出现的位置
//            $table_name = substr($class_name,0,strpos($class_name,"Model"));
            $table_name = substr($class_name,0,-5);
            $this->table_name = strtolower($table_name);
        }
        return '`'.$GLOBALS['config']['db']['prefix'].$this->table_name.'`';
    }

    /**
     * 根据传入条件获取所有数据
     * @param string $condition 条件 格式 key1=value1 and key2=value2
     * @return array|二维数组
     */
    public function getAll($condition = ''){
        $sql = "select * from {$this->table()}";
        if (!empty($condition)) {
            $sql .= " where ".$condition;
        }
        //2.执行sql
        $rows = $this->db->fetchAll($sql);
        return $rows;
    }
    /**
     * 根据 主键 删除一行数据
     * @param $pk 主键
     */
    public function deleteByPk($pk){
        //1.准被sql
        $sql = "delete from {$this->table()} WHERE `{$this->fields['pk']}`='{$pk}'";
        //2.执行
        $this->db->query($sql);
    }

    /**
     * 根据id获取一行数据
     * @param $pk 主键
     * @return null|一维数组
     */
    public function getByPk($pk){
        //1.准被sql
        $sql = "select * from {$this->table()} WHERE `{$this->fields['pk']}`='{$pk}' limit 1";
        //2.执行
        return $this->db->fetchRow($sql);
    }
    /**
     * 过滤掉错误的字段
     * @param $data
     */
    private function ignoreErrorField(&$data){
        //过滤掉多余字段
        foreach($data as $k=>$v){
            if (!in_array($k,$this->fields)) {
                unset($data[$k]);
            }
        }
    }
    /**
     * 将数据添加到数据库
     *需要准备的sql
     * insert into brand set name='小米',url='wwww.xiaomi.com',logo='',intro='为发烧而生！！！！';
        array(4) {
        ["name"]=>
        string(6) "小米"
        ["url"]=>
        string(15) "wwww.xiaomi.com"
        ["logo"]=>
        string(0) ""
        ["intro"]=>
        string(27) "为发烧而生！！！！"
        }
     *
        $fieldsValue = [
        "name='小米'",
        "url='wwww.xiaomi.com'",
        "logo=''",
        "intro='为发烧而生！！！！'"
        ];
        implode(',',$fieldsValue);
     *
        insert into {$this->table()} set . implode(',',$fieldsValue)
     *
     *
     * @param $data
     */
    public function insertData($data){
        //过滤掉多余字段
        $this->ignoreErrorField($data);
        //1.准被sql
        $sql = "insert into {$this->table()} set ";
        //存放 "key=$value"
        $fieldsValue = [];
        foreach($data as $k=>$v){
            $fieldsValue[] = "`{$k}`='{$v}'";
        }
        //2.执行
        $sql .= implode(',',$fieldsValue);
        $this->db->query($sql);
        return $this->db->last_insert_id();
    }

    /**
     * 更新数据
     * @param $data 必须包含pk主键的数组
     */
    public function updateData($data){
        //过滤掉多余字段
        $this->ignoreErrorField($data);
        //1.准被sql
        $sql = "update {$this->table()} set ";
        //存放 "key=$value"
        $fieldsValue = [];
        foreach($data as $k=>$v){
            $fieldsValue[] = "`{$k}`='{$v}'";
        }
        $sql .= implode(',',$fieldsValue)." where `{$this->fields['pk']}`={$data[$this->fields['pk']]}";
        //2.执行
        $this->db->query($sql);
    }

    /**
     * 根据条件统计总条数
     * @param $condition
     */
    public function getCount($condition = ''){
        $sql = "select count(*) from {$this->table()}";
        if (!empty($condition)) {
            $sql .= " where {$condition}";
        }
        return $this->db->fetchColumn($sql);
    }

    /**
     * 根据条件获取一条数据
     * @param $condition
     * @return null|一维数组
     */
    public function getRow($condition){
        $sql = "select * from {$this->table()} WHERE {$condition} limit 1";
        return $this->db->fetchRow($sql);
    }

    /**
     * 根据传入条件获取一行的某个字段的值
     * @param $field
     * @param $condition
     */
    public function getColumn($field,$condition){
        $sql = "select `{$field}` from {$this->table()} WHERE {$condition} limit 1";
        return $this->db->fetchColumn($sql);
    }
}