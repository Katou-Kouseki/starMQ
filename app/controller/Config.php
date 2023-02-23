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
use app\model\Config as C;

class Config extends Base
{
    /**
     * 应用中间件
     *
     * @var array[]
     */
    protected $middleware = [
        Check::class => []
    ];

    /**
     * 修改发信设置
     *
     * @param Request $request
     *
     * @return \think\Response|\think\response\Redirect
     */
    public function smtp(Request $request)
    {
        if (!$request->isPost())return redirect("/User/index");
        C::where("key", "smtp_host")->update(["val" => $request->post("smtp_host")]);
        C::where("key", "smtp_port")->update(["val" => $request->post("smtp_port")]);
        C::where("key", "smtp_user")->update(["val" => $request->post("smtp_user")]);
        C::where("key", "smtp_pass")->update(["val" => $request->post("smtp_pass")]);
        $this->writelog("修改发信设置", 1);
        return $this->ResJson(["code" => 200, "msg" => "提交成功!", "data" => NULL]);
    }

    /**
     * 修改系统设置方法
     *
     * @param Request $request
     *
     * @return \think\Response|\think\response\Redirect
     */
    public function sys(Request $request)
    {
        if (!$request->isPost()) return redirect("/User/index");
        $data = $request->post();
        C::where("key", "close_time")->update(["val" => $data["close_time"]]);
        C::where("key", "sitename")->update(["val" => $data["sitename"]]);
        C::where("key", "desc")->update(["val" => $data["desc"]]);
        C::where("key", "yuyin")->update(["val" => $data["yuyin"]]);
        C::where("key", "tips")->update(["val" => $data["tips"]]);
        C::where("key", "is_tips")->update(["val" => $data["is_tips"]]);
        C::where("key", "beian")->update(["val" => $data["beian"]]);
        C::where("key", "callback")->update(["val" => $data["callback"]]);
        $this->writelog("修改系统设置", 1);
        return $this->ResJson(["code" => 200, "msg" => "提交成功!", "data" => NULL]);
    }

    /**
     * 重置APPKEY
     *
     * @param Request $request
     *
     * @return \think\Response|\think\response\Redirect
     */
    public function rekey(Request $request)
    {
        if (!$request->isPost()) return redirect("/User/index"); //如果不是POST就重定向到首页
        $res = C::where("key", "appkey")->update(["val" => strtoupper(md5(time()))]); //更新APPKEY
        if ($res) {
            $this->writelog("重置密钥", 1);
            return $this->ResJson(["code" => 200, "msg" => "重置成功!", "data" => NULL]);
        } else {
            $this->writelog("重置密钥", 0);
            return $this->ResJson(["code" => 201, "msg" => "重置失败!", "data" => NULL]);
        }
    }
}