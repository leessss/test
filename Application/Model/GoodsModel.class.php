<?php

/**
 * 商品模型
 */
class GoodsModel extends Model
{
    /**
     * 获取所有列表数据
     * @return array|二维数组
     */
    public function getGoodsList($condition){
        //1.获取商品所有数据
            $rows = parent::getAll($condition);
        //2.处理商品状态
            foreach($rows as $key=>&$row){
//                $rows[$key]['is_best'] = ($row['status'] & 1)==1 ? 1 : 0;
//                $rows[$key]['is_new'] = ($row['status'] & 2)==2 ? 1 : 0;
//                $rows[$key]['is_hot'] = ($row['status'] & 4)==4 ? 1 : 0;
                $row['is_best'] = ($row['status'] & 1)==1 ? 1 : 0;
                $row['is_new'] = ($row['status'] & 2)==2 ? 1 : 0;
                $row['is_hot'] = ($row['status'] & 4)==4 ? 1 : 0;
            }
        //3.返回处理后的结果
            return $rows;
    }


    /**
     * 获取所有列表数据
     * @return array|二维数组
     */
    public function getPageResult($page){
        //每页显示条数
        $pageSize = 5;

        //获取总条数
        $count = parent::getCount();

        //总页数
        $total_page = ceil($count/$pageSize);
        if ($total_page<$page) {
            $page = $total_page;
        }
        $page = (is_numeric($page) && $page>=1) ? $page : 1;
        //起始
        $start = ($page-1)*$pageSize;

        //1.获取商品每页数据
        $rows = parent::getAll("1=1 limit {$start},{$pageSize}");

        //2.处理商品状态
        foreach($rows as $key=>&$row){
            $row['is_best'] = ($row['status'] & 1)==1 ? 1 : 0;
            $row['is_new'] = ($row['status'] & 2)==2 ? 1 : 0;
            $row['is_hot'] = ($row['status'] & 4)==4 ? 1 : 0;
        }
        //3.返回处理后的结果
        return ['rows'=>$rows,'count'=>$count,'page'=>$page,'pageSize'=>$pageSize];
    }
    /**
     * 完成商品添加
     * @param $data
     */
    public function add($data){
        //求出商品状态的或运算
        $status = 0;//初始状态为 零
        if (isset($data['status'])){//先判断是否有该字段 加入什么都没有点就不会将该字段传过来
            foreach($data['status'] as $v){
                $status = $status | $v;
            }
        }
        $data['status'] = $status;//将最后获取的状态覆盖原来的的状态
        //添加 添加时间
        $data['add_time'] = time();
        return parent::insertData($data);
    }
}