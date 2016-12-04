<?php

/**
 * 商品控制器
 */
class GoodsController extends PlatformController
{
    /**
     * 商品列表页
     */
    public function index(){
        //1.接收请求数据
        /**
         * 匹配逻辑
         * select * from goods where
         * category_id=5
         * and status & {$_POST['status']}> 0
         * and is_on_sale = $_POST['is_on_sale']-1
         * and (name like '%{$_POST['keyword']}%' or sn like '%{$_POST['keyword']}%')
         */
            //准备一个添加数组
            $condition = [];
            //检测分类id是否为空
            if(!empty($_POST['category_id'])){
                $condition[] = "category_id={$_POST['category_id']}";
            }
            //判断是否传入状态
            if(!empty($_POST['status'])){
                $condition[] = "status & {$_POST['status']} > 0";
            }
            //判断是否上架
            if(!empty($_POST['is_on_sale'])){
                $_POST['is_on_sale'] = $_POST['is_on_sale']-1;
                $condition[] = "is_on_sale = {$_POST['is_on_sale']}";
            }
            if(!empty($_POST['keyword'])){
                $condition[] = "(name like '%{$_POST['keyword']}%' or sn like '%{$_POST['keyword']}%')";
            }
            //将所有条件拼接到一起
            $condition = implode(" and ",$condition);
        //2.处理数据
            $goodsModel = new GoodsModel();
            //帮我获取商品列表
            $rows = $goodsModel->getGoodsList($condition);
            //获取商品分类数据
            $categoryModel = new CategoryModel();
            $categorys = $categoryModel->getList();
        //3.展示页面
            $this->assign("rows",$rows);
            //分配商品分类数据
            $this->assign("categorys",$categorys);
            $this->display('index');
    }

    /**
     * 商品列表 分页
     */
    public function page(){
        //1.接收请求数据
        $page = isset($_GET['page'])?$_GET['page']:1;
        //2.处理数据
        $goodsModel = new GoodsModel();
        /**
         * 期望该方法根据传入页码来获取分页数据
         * 每页的数据   $rows
         * 获取总条数据 $count
         * 当前页      $page
         * 每页显示条数  $pageSize
         *
         * return [
         * 'rows'=>$rows,
         * 'count'=>$count,
         * 'page'=>$page,
         * 'pageSize'=>$pageSize
         * ]
         */
        $pageResult = $goodsModel->getPageResult($page);
        /**
         * 期望该类的方法根据
         * 传入的链接，
         * 总条数，
         * 当前页码，
         * 每页显示条数
         * 来帮我们生成分页工具
         */
        $page_html = PageTool::show("index.php?p=Admin&c=Goods&a=page",$pageResult['count'],$pageResult['page'],$pageResult['pageSize']);
        //将分页代码分配到页码显示
        $this->assign("page_html",$page_html);
        //获取商品分类数据
        $categoryModel = new CategoryModel();
        $categorys = $categoryModel->getList();
        //3.展示页面
        $this->assign("rows",$pageResult['rows']);
        //分配商品分类数据
        $this->assign("categorys",$categorys);
        $this->display('page');
    }
    /**
     * 完成添加和展示添加
     */
    public function add(){
        if ($_SERVER['REQUEST_METHOD'] == "POST"){
            //1.接收请求数据
                $data = $_POST;
                //指定只能上传规定大小和类型的图片
//                $max_size = 1024*1024*2;
//                $allow_types = ['image/png','image/jpeg','image/gif','image/bmp'];
                $uploadTool = new UploadTool();
//                $uploadTool->max_size = 1024*1024*2;
//                $uploadTool->allow_types = ['image/jpeg','image/gif','image/bmp'];
                //期望该方法成功返回图片路径，失败false
                $logo = $uploadTool->uploadOne($_FILES['logo'],"goods/");
                if ($logo !== false) {
                    //上传图片成功
                    $data['logo'] = $logo;

                    //制作缩略图
                    $imageTool = new ImageTool();
                    /**
                     * 期望thumb方法根据图片路径制作指定大小一张缩略图，
                     * 并且返回制作好后的缩略图路径
                     * 明确缩略图文件名：5839360d157e0_60x60.png
                     */
                    $thumb_logo = $imageTool->thumb($logo,60,60);
                    if($thumb_logo !== false){
                        //上传成功后保存到$data , 为了将缩略图保存到数据库
                        $data['thumb_logo'] = $thumb_logo;
                    }else{
                        $this->redirect('index.php?p=Admin&c=Goods&a=add','制作缩略图失败！'.$imageTool->getError(),3);
                    }
                }else{
                    $this->redirect("index.php?p=Admin&c=Goods&a=add","商品图片上传失败！".$uploadTool->getError(),3);
                }
            //2.处理数据
                $goodsModel = new GoodsModel();
                $goods_id = $goodsModel->add($data);//期望该方法帮我们返回一个添加后产生的id
                //处理相册 只有全部上传成功才算成功
                    $gallerys = $uploadTool->uploadMore($_FILES['img_url'],'gallery/');

                    if ($gallerys !== false) {
                        //上传成功后保存到相册表中
                        //创建相册对象
                        $galleryModel = new GalleryModel();
                        //重组相册信息
                        foreach($gallerys as $key=>$gallery){
                            $gallery_data = [];//需要保存到相册表的信息
                            $gallery_data['img_url'] = $gallery;//保存图片链接
                            $gallery_data['image_intro'] = $_POST['image_intro'][$key];
                            $gallery_data['url'] = $_POST['url'][$key];
                            $gallery_data['goods_id'] = $goods_id;
                            //处理数据
                            //调用相册对象上的方法添加数据
                            $galleryModel->insertData($gallery_data);
                        }
                    }else{
                        $this->redirect("index.php?p=Admin&c=Goods&a=index",'商品添加成功相册添加失败！',3);
                    }
            //3.展示页面
                $this->redirect("index.php?p=Admin&c=Goods&a=index");
        }else{
            //1.接收请求数据
            //2.处理数据
                //将分类数据分配到页面
                $categoryModel = new CategoryModel();
                $categorys = $categoryModel->getList();
            //3.展示页面
                $this->assign("categorys",$categorys);
                $this->display("add");
        }
    }
}