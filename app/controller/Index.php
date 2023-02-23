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

use app\model\Config as C;
use star\Http;

class Index extends Base
{
    public function index()
    {
        $this->assign($this->conf()); //模板变量赋值
        return $this->fetch();//渲染视图
    }

    //App心跳接口
    public function appHeart()
    {
        $key = $this->data["c"]["appkey"];
        $t = input("t");
        $_sign = $t . $key;
        if (md5($_sign) != input("sign")) {
            return json(["code" => -1, "msg" => "签名校验不通过", "data" => null]);
        }
        C::where("key", "app_heart")->update(["val" => time()]);
        C::where("key", "app_status")->update(["val" => 1]);
        return json(["code" => 1, "msg" => "成功", "data" => null]);
    }


    //App推送付款数据接口
    public function appPush()
    {
        $key = $this->data["c"]["appkey"];
        $t = input("t");
        $type = input("type");
        $money = input("money");
        $_sign = $type . $money . $t . $key;
        if (md5($_sign) != input("sign")) {
            return json(["code" => -1, "msg" => "签名校验不通过", "data" => null]);
        }

        $res = \app\model\Order::where("really_money", $money)
            ->where("status", 0)
            ->where("type", $type)
            ->find();

        if ($res) {
            \app\model\Order::where("id", $res['id'])->update([
                "status" => 1,
                "pay_time" => time()
            ]);
            $res["pid"] = $this->data["c"]["appid"];
            $u = $this->create_call($res,$key);
            if ($this->data["c"]["callback"] == "0"){
                $re = Http::get($u['notify']);
            }else{
                $re = Http::post($u['notify']);
            }
            if ($re == "success") {
                return json(["code" => 1, "msg" => "成功", "data" => null]);
            } else {
                \app\model\Order::where("id", $res['id'])->update(["state" => 0]);
                return json(["code" => 1, "msg" => "异步通知失败", "data" => null]);
            }
        }
    }

    /**
     * 计划任务监控
     *
     * @return void
     */
    public function job()
    {
        if ((time() - 60) > $this->data["c"]["app_heart"]){
            C::where("key", "app_status")->update(["val" => 0]);
            if ($this->data["c"]["is_tips"] == "1"){
                $this->mail("APP监控异常");
                return;
            }
            echo '{"code": 201, "msg": "APP监控异常", "time": '.time().'}\n';
        } else{
            echo '{"code": 200, "msg": "APP监控正常", "time": '.time().'}\n';
        }
    }
}
