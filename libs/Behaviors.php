<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace tools;

class Behaviors
{
    /**
     * 加载系统配置 初始化session
     */
    public function app_begin()
    {
        \tools\Config::load();
    }

    /**
     * 定义系统常量
     */
    public function module_init()
    {
        $request = \think\Request::instance();
        $method  = $request->method();

        define('IS_GET', $method == 'GET' ? true : false);
        define('IS_POST', $method == 'POST' ? true : false);
        define('IS_PUT', $method == 'PUT' ? true : false);
        define('IS_DELETE', $method == 'DELETE' ? true : false);
        define('IS_AJAX', $request->isAjax());

        define('NOW_TIME', $request->time());
        define('MODULE_NAME', $request->module());
        define('CONTROLLER_NAME', $request->controller());
        define('ACTION_NAME', $request->action());

        define('__SELF__', $request->url(true));
    }

    /**
     * 返回头修改
     */
    public function app_end($data, $res)
    {
        $res->header('X-Powered-By', 'cjango.com');
    }
}
