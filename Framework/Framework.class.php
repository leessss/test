<?php

/**
 * 框架基础类
 */
class Framework
{
    /**
     * 帮我们初始化所有数据
     */
    public static function run(){
        //使用这个spl_autoload_register()将  userAutoLoad() 注册到 __autoload队列中
//        spl_autoload_register("Framework::userAutoload");
        spl_autoload_register(array(self::class,"userAutoload"));

        self::initPath();//初始化路径常量
        self::initConfig();//初始化配置文件
        self::initRequestParams();//初始化请求参数

        self::initClassMapping();//初始化框架映射类 （必须先映射再请求分发）
        self::dispatch();//请求分发
    }
    private static function initPath(){
        /**
         * 定义项目的目录常量  要求所有目录都以 / 结尾
         */
        defined("DS") or define("DS",DIRECTORY_SEPARATOR);
        defined("ROOT_PATH") or define("ROOT_PATH",dirname($_SERVER['SCRIPT_FILENAME']).DS);
        defined("APP_PATH") or define("APP_PATH",ROOT_PATH."Application".DS);//application路径
        defined("FRAME_PATH") or define("FRAME_PATH",ROOT_PATH."Framework".DS);//Framework的路径
        defined("PUBLIC_PATH") or define("PUBLIC_PATH",ROOT_PATH."Public".DS);//Public的路径
        defined("UPLOADS_PATH") or define("UPLOADS_PATH",ROOT_PATH."Uploads".DS);//Uploads的路径
        defined("CONFIG_PATH") or define("CONFIG_PATH",APP_PATH."Config".DS);//config的路径
        defined("CONTROLLER_PATH") or define("CONTROLLER_PATH",APP_PATH."Controller".DS);//Controller的路径
        defined("MODEL_PATH") or define("MODEL_PATH",APP_PATH."Model".DS);//Model的路径
        defined("VIEW_PATH") or define("VIEW_PATH",APP_PATH."View".DS);//View的路径
        defined("TOOLS_PATH") or define("TOOLS_PATH",FRAME_PATH."Tools".DS);//Tools的路径
    }

    private static function initConfig(){
        //加载配置信息
        $GLOBALS['config'] = require CONFIG_PATH."application.config.php";
    }

    private static function initRequestParams(){
        //1,接收url参数
        $a = isset($_GET['a']) ? $_GET['a'] : $GLOBALS['config']['default']['default_action'];//调用的方法
        $c = isset($_GET['c']) ? $_GET['c'] : $GLOBALS['config']['default']['default_controller'];//控制器
        $p = isset($_GET['p']) ? $_GET['p'] : $GLOBALS['config']['default']['default_platform'];//平台
        //当前控制器路径
        defined("CURRENT_CONTROLLER_PATH") or define("CURRENT_CONTROLLER_PATH",CONTROLLER_PATH.$p.DS);
        //当前视图所在路径
        defined("CURRENT_VIEW_PATH") or define("CURRENT_VIEW_PATH",VIEW_PATH.$p.DS.$c.DS);

        //定义常量的控制器，方法，平台
        defined("ACTION_NAME") or define("ACTION_NAME",$a);
        defined("CONTROLLER_NAME") or define("CONTROLLER_NAME",$c);
        defined("PLATFORM_NAME") or define("PLATFORM_NAME",$p);
    }

    private static function dispatch(){
        //3.创建控制器对象
        $class_name = CONTROLLER_NAME."Controller";//类名
        $controller = new $class_name();//可变类名
        //4.调用方法
        $action_name = ACTION_NAME;
        $controller->$action_name();//可变方法  方法名可以使用一个变量代替
    }
    private static function initClassMapping(){
        //映射框架类文件
        $GLOBALS['classMapping'] = [
            'DB'=>TOOLS_PATH."DB.class.php",
            'Model'=>FRAME_PATH."Model.class.php",
            'Controller'=>FRAME_PATH."Controller.class.php"
        ];
    }
    private static function userAutoLoad($class_name){
        if(isset($GLOBALS['classMapping'][$class_name])){//映射框架类
            require $GLOBALS['classMapping'][$class_name];
        }elseif (substr($class_name,-10) == "Controller"){//加载控制器
            require CURRENT_CONTROLLER_PATH.$class_name.".class.php";
        }elseif(substr($class_name,-5) == "Model"){//加载模型类
            require MODEL_PATH.$class_name.".class.php";
        }elseif(substr($class_name,-4) == "Tool"){
            require TOOLS_PATH.$class_name.".class.php";
        }
    }
}