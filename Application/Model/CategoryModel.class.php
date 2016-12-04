<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17 0017
 * Time: 上午 11:50
 */
class CategoryModel extends Model
{
    /**
     * 完成排序和缩进的功能
     */
    public function getList($parent_id = 0){
        //1,获取所有数据
        $rows = parent::getAll();
        //2.排序 缩进
        $rows = $this->getChildren($rows,$parent_id,0);
        return $rows;
    }

    /**
     * 帮我们获取儿子
     * @param $rows 所有数据
     * @param $parent_id 父分类id
     * @param $deep 节点深度
     */
    private function getChildren(&$rows,$parent_id,$deep = 0){
        static $children = [];//帮我们保存找到的所有子孙
        foreach($rows as $child){//循环遍历所有数据，获取需要的节点
            if ($child['parent_id'] == $parent_id) {
                $child['name_text'] = str_repeat("---",$deep*2).$child['name'];
                $children[] = $child;//节点AAA
                //再次调用找儿子的方法
                $this->getChildren($rows,$child['id'],$deep+1);
            }
        }
        return $children;
    }

    /**
     * 删除
     * @param $id
     */
    public function delete($id){
        //检测当前分类下是否有子分类，如果没有才删除，如果有就不删除
//        "select count(*) from category WHERE parent_id={$id}";
        $count = $this->getCount("parent_id={$id}");
        if ($count > 0) {
            $this->error = "当前分类下有子分类，不能直接删除";
            return false;
        }
        parent::deleteByPk($id);
    }

    /**
     * 根据数据:
     * @param $data  大大的前提:  必须是一个关联数组, 键必须和数据库中的字段一一对应.
     * @return 保存在数据库中数据对应的id
     */
    public function insertData($data)
    {
        if (empty($data['name'])) {
            $this->error = "商品分类名称不能为空！";
            return false;
        }
        //同一父分类下面不能出现相同名字的分类
        //"select count(*) from category WHERE parent_id={$data['parent_id']} and name='{$data['name']}'";
        $count = $this->getCount("parent_id={$data['parent_id']} and name='{$data['name']}'");
        if ($count > 0) {
            $this->error = "在当前分类下面分类名称重名！";
            return false;
        }
        parent::insertData($data);
    }

    /**
     * @param 必须包含主键的值 $data
     */
    public function updateData($data){
        //1.商品分类名称不能为空
        if (empty($data['name'])) {
            $this->error = "商品分类名称不能为空！";
            return false;
        }
        //2.商品分类名称不能与同级分类的其他分类名称相同
        //"select COUNT(*) from category WHERE parent_id={$data['parent_id']} and name='{$data['name']}' and id <> {$data['id']}";
        $count = $this->getCount("parent_id={$data['parent_id']} and name='{$data['name']}' and id <> {$data['id']}");
        if ($count > 0) {
            $this->error = "商品分类名称不能与同级分类的其他分类名称相同";
            return false;
        }
        //3.不能修改到自己的子孙分类下面和自己下面
        $ids = [];//存放不能使用的parent_id
        //获取到当前分类的所有子孙
        $children = $this->getList($data['id']);
        $ids = array_column($children,"id");//所有子孙的id
        $ids[] = $data['id'];//自己的id
        if (in_array($data['parent_id'],$ids)) {
            $this->error = "不能修改到自己的子孙分类下面和自己下面";
            return false;
        }
        //更新数据
        parent::updateData($data);
    }
}