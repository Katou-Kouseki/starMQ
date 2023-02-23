<?php
/**
 * @Describe:
 * @FileName: Pay.php
 * @Date    : 2023/2/23
 * @Author  : 深秋.
 * @Email   : <i@kain8.cn>
 */

namespace app\controller;

use star\Epay;
use think\facade\Request;
use think\facade\View;
use app\model\Order as O;
use app\model\Code as C;

class Pay extends Base
{
    public function submit()
    {
        //        $this->closeEndOrder();
        $key = $this->data["c"]["appkey"];
        $data = Request::param('', '', 'strip_tags');
        if (empty($data['pid'])) {
            View::assign('error_tips', "PID不可为空");
            return View::fetch();
        }
        if (empty($data['out_trade_no'])) {
            View::assign('error_tips', "订单号不可为空");
            return View::fetch();
        }
        if (empty($data['type'])) {
            View::assign('error_tips', "支付类型不可为空");
            return View::fetch();
        }
        if (empty($data['notify_url'])) {
            View::assign('error_tips', "异步通知地址不可为空");
            return View::fetch();
        }
        if (empty($data['return_url'])) {
            View::assign('error_tips', "同步通知地址不可为空");
            return View::fetch();
        }
        if (empty($data['name'])) {
            View::assign('error_tips', "商品名称不可为空");
            return View::fetch();
        }
        if (empty($data['money'])) {
            View::assign('error_tips', "金额不可为空");
            return View::fetch();
        }
        if ($data['pid'] != $this->data["c"]["appid"]) {
            View::assign('error_tips', "商户不存在");
            return View::fetch();
        }
        if ($data['money'] <= 0) {
            View::assign('error_tips', "金额错误");
            return View::fetch();
        }
        $epay = new Epay();
        $isSign = $epay->getEpaySignVeryfy($data, $data["sign"], $key); //生成签名结果
        if (!$isSign) {
            View::assign('error_tips', "验签失败,请检查PID或者Key是否正确");
            return View::fetch();
        }
        $is_orderNo = O::where('out_trade_no', $data['out_trade_no'])->find();
        if ($is_orderNo) {
            View::assign('error_tips', "订单号重复,请重新发起");
            return View::fetch();
        }
        $appjk = $this->data["c"]["app_status"];//app监控状态
        $pcjk = $this->data["c"]["pc_status"];//PC监控状态
        if ($appjk != "1" || $pcjk != "1") {
            View::assign('error_tips', "监控端状态异常，请检查");
            return View::fetch();
        }
        $reallyMoney = bcmul($data["money"], 100);

        $trade_no = "S-" . date("YmdHis") . rand(1, 9) . rand(1, 9) . rand(1, 9) . rand(1, 9);
        $order = O::where("status", 0)->select()->toArray();
        foreach ($order as $k => $v){
            if ($v["create_time"] < (time()-$this->data["c"]["close_time"]) ){
                if ($v["money"] == $reallyMoney){
                    $reallyMoney ++;
                }else{
                    break;
                }
            }else{
                break;
            }
        }

        $reallyMoney = bcdiv($reallyMoney, 100, 2);

        $db = [
            "create_time" => time(),
            "pay_time" => 0,
            "out_trade_no" => $data["out_trade_no"],
            "trade_no" => $trade_no,
            "name" => $data["name"],
            "money" => $data["money"],
            "really_money" => $reallyMoney,
            "sitename" => isset($data["sitename"]) ? $data["sitename"] : '',
            "ip" => Request::ip(),
            "return_url" => $data["return_url"],
            "notify_url" => $data["notify_url"],
            "type" => $data["type"],
            "status" => 0,
        ];
        $res = O::insert($db);
        if ($res){
            exit("<script>window.location.href='/Pay/console?trade_no={$trade_no}';</script>");
        }else{
            View::assign('error_tips', "订单生成错误,请重新发起支付");
            return $this->fetch();
        }
    }

    public function console($trade_no = "")
    {
        if (Request::isPost()) {
            $data = Request::param('', '', 'strip_tags');
            $trade_no = $data['trade_no'];

            if (empty($trade_no)) {
                return json(['code' => 0, 'msg' => '订单号为空!']);
            }
            $res = O::where('trade_no', $trade_no)->find();
            if (empty($res)) {
                return json(['code' => 0, 'msg' => '订单不存在!']);
            }
            if ($res['status'] == -1) {
                return json(['code' => 0, 'msg' => '订单已过期!']);
            }
            if ($res['status'] == 0) {
                return json(['code' => 0, 'msg' => '获取二维码成功!']);
            }
            $key = $this->data["c"]["appkey"];
            $res["pid"] = $this->data["c"]["appid"];
            $res['money'] = number_format($res['money'], 2, ".", "");
            $res['really_money'] = number_format($res['really_money'], 2, ".", "");

            $u = $this->create_call($res,$key);
            return json(['code' => 200, 'msg' => '订单支付成功!', 'url' => $u['return']]);
        }
        $db = O::where("trade_no", $trade_no)->find();
        $pay_url = C::where("type", $db["type"])->select()->toArray();
        $pay_url = $pay_url[rand(0, (count($pay_url) -1) )]["url"];  //随机取一条收款码
        $time = $this->data["c"]["close_time"]; //订单关闭时间
        $type = $db['type']; //支付类型
        $money = $db['really_money']; //支付金额
        $tips = $this->data["c"]["tips"]; //提示
        $yuyin = $this->data["c"]["yuyin"]; //语音
        $name = $db["name"];
        View::assign([
            "order" => $trade_no,
            "type" => $type,
            "money" => $money,
            "payurl" => $pay_url,
            "time" => $time,
            "tips" => $tips,
            "yuyin" => $yuyin
            ,"name" =>$name
        ]);
        return View::fetch();
    }
}