<?php

/**
 * 作者: 深秋
 * QQ : 1361582519
 * 官方QQ群: 758107405
 * GitHub: https://github.com/kaindev8/starMQ
 * 保留版权信息，尊重开源精神!
 * 禁止修改此文件!
 */

namespace app\controller;

use think\facade\Cache;


class Install extends Base
{
    public function index()
    {
        Cache::clear();
        return $this->fetch();
    }
}
