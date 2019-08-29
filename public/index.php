<?php

//define('APP_DEBUG', true);
define('APP_DEBUG', false);

define('ROOT_URL', 'http://' . substr(ROOT_DOMAIN, 1));
define('DEF_AVATAR', 'static/index/img/default_avatar.jpg');
/**
 * 系统版本
 */
define('VERSION', '1.0 beta');
/**
 * 定义项目路径
 */
define('APP_PATH', __DIR__ . '/../application/');
/**
 * 修改缓存目录
 */
define('RUNTIME_PATH', __DIR__ . '/../runtime/');
/**
 * 加载框架引导文件
 */
require __DIR__ . '/../thinkphp/start.php';
