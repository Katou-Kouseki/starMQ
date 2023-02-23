<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
/**
 * 作者: 深秋
 * QQ : 1361582519
 * 官方QQ群: 758107405
 * GitHub: https://github.com/kaindev8/starMQ
 * 保留版权信息，尊重开源精神!
 * 禁止修改此文件!
 */
// [ 应用入口文件 ]
namespace think;

require __DIR__ . '/../vendor/autoload.php';
//定义分隔符
define('DS', DIRECTORY_SEPARATOR);
define("APP_VERSION", "v2.0.0");
define("AUTHOR", "758107405");
// 执行HTTP应用并响应
$app = new App();
$http = $app->http;
// 检测程序安装
if(is_file(__DIR__ . '/install.lock')){
    header("location:./");
    exit;
}
$app->route->rule('','/Install/index');
// 应用入口
$response = $http->run();

$response->send();

$http->end($response);