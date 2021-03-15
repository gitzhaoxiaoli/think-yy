<?php
declare (strict_types = 1);

namespace yy;
use think\App;
/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [
        //\app\middleware\Check::class,
    ];


    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
        $this->init();
    }

    // 初始化
    protected function initialize(){}

    protected function init(){}

    public function __call($method, $args)
    {
        return api([], 404, '', new \Exception());
    }

}
