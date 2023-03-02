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

use app\middleware\Check;
use think\Request;

class Log extends Base
{
    protected $middleware = [Check::class => []];

    /**
     * 清除操作日志
     *
     * @param Request $request
     *
     * @return \think\Response|\think\response\Redirect
     */
    public function delog(Request $request)
    {
        if (!$request->isPost()) return redirect("/User/index");
        $res = $this->log->where("id", ">", 0)->delete();
        if ($res){
            $this->writelog("清除操作日志", 1);
            return $this->ResJson(["code" => 200, "msg" => "清除日志成功!", "data" => NULL]);
        }else{
            $this->writelog("清除操作日志", 0);
            return $this->ResJson(["code" => 201, "msg" => "清除日志失败!", "data" => NULL]);
        }
    }
}