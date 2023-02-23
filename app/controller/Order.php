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
use star\Http;
use think\Request;
use app\model\Order as O;

class Order extends Base
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
     * 删除单条订单
     *
     * @param Request $request
     *
     * @return \think\Response|\think\response\Redirect|void
     */
    public function del(Request $request)
    {
        if(!$request->isPost()) return redirect("/User/index");
        $id = $request->post("id");
        $res = O::delete($id);
        if ($res){
            $this->writelog("删除单条订单", 1);
            return $this->ResJson(["code" => 200, "msg" => "删除成功", "data" => NULL]);
        }
    }


    /**
     * 补单方法
     *
     * @param Request $request
     *
     * @return \think\Response|\think\response\Redirect|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function budan(Request $request)
    {
        if (!$request->isPost()) return redirect("/User/index");
        $id = $request->post("id");
        $order = O::find($id);
        if (empty($order)) return $this->ResJson(["code" => 201, "msg" => "此订单不存在!", "data" => NULL]);
        $key = $this->data["c"]["appkey"]; //APPKEY
        $order["pid"] = $this->data["c"]["appid"];
        $url = $this->create_call($order, $key);
        if ($this->data["c"]["callback"] == "0") $res = Http::get($url["notify"]);
        if ($this->data["c"]["callback"] == "1") $res = Http::post($url["notify"]);
        if ($res == "success"){
            $this->writelog("补单", 1);
            return $this->ResJson(["code" => 200, "msg" => "补单成功!", "data" => NULL]);
        }
        if ($res == "fail"){
            $this->writelog("补单", 0);
            return $this->ResJson(["code" => 201, "msg" => "补单失败!", "data" => NULL]);
        }
    }

    /**
     * 清除未支付订单
     *
     * @param Request $request
     *
     * @return \think\Response|\think\response\Redirect
     */
    public function deorder(Request $request)
    {
        if (!$request->isPost())return redirect("/User/index"); //如果不是POST就重定向到首页
        $res = O::where("status != 1")->delete(); //删除所有未支付订单
        if ($res != 0) {
            $this->writelog("清除未支付订单", 1);
            return $this->ResJson(["code" => 200, "msg" => "清除成功!", "data" => NULL]);
        } else {
            $this->writelog("清除未支付订单", 0);
            return $this->ResJson(["code" => 201, "msg" => "清除失败!", "data" => NULL]);
        }
    }
}