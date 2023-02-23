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

use app\BaseController;
use app\model\Admin as A;
use app\model\Config as C;
use app\model\Log as L;
use app\model\Order as O;
use app\model\Code as D;
use think\facade\Config as Conf;
use mailer\think\Mailer as M;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;

class Base extends BaseController
{
    /**
     * 公共数据
     *
     * @var array
     */
    protected $data = [];

    protected $log;

    /**
     * 初始化
     *
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function initialize()
    {
        $this->log  = new L();
        $order_list = $this->order();
        $logs       = $this->log();
        $code       = $this->channellist();
        $this->data = [
            "u" => $this->u(), //用户信息
            "c" => $this->conf(), //系统设置信息
            "o" => $this->census(),//统计信息
            "s" => $this->server_info(),//服务器信息
            "l" => $order_list["l"],//订单列表
            "p" => $order_list["p"],//订单列表分页
            "g" => $logs["l"],//日志列表
            "x" => $logs["p"],//日志列表分页
            "d" => $code["l"],//通道列表
            "a" => $code["p"],//通道列表分页
        ];
        $this->assign($this->data);//模板变量
    }

    /**
     * 日志记录方法
     *
     * @param string  $event
     * @param Request $request
     *
     * @return true
     */
    protected function writelog(string $event, int $status)
    {
        $this->log->save([
            "ip"        =>  Request::ip(),
            "time"      =>  time(),
            "event"     =>  $event,
            "status"    =>  $status,
            "addres"    =>  addres(Request::ip())
        ]);
        return true;
    }

    /**
     * 查询订单列表
     *
     * @return array
     * @throws \think\db\exception\DbException
     */
    private function order()
    {
        $list = O::order("id","desc")->paginate(10);
        $page = $list->render();
        return ["l" => $list, "p" => $page];
    }

    /**
     * 查询日志
     *
     * @return array
     * @throws \think\db\exception\DbException
     */
    private function log()
    {
        $log    =   L::order("id", "desc")->paginate([ "list_rows" => 10]);
        $page   =   $log->render();
        return ["l" => $log, "p" => $page];
    }

    /**
     * 系统设置信息
     *
     * @return array $conf
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function conf()
    {
        $conf["app_heart"]      =       C::where("key", "app_heart")->find()["val"];//APP心跳时间
        $conf["app_status"]     =       C::where("key", "app_status")->find()["val"];//APP监控状态
        $conf["appid"]          =       C::where("key", "appid")->find()["val"];//APPID
        $conf["appkey"]         =       C::where("key", "appkey")->find()["val"];//APPKEY
        $conf["beian"]          =       C::where("key", "beian")->find()["val"];//备案号
        $conf["callback"]       =       C::where("key", "callback")->find()["val"];//异步回调方式
        $conf["close_time"]     =       C::where("key", "close_time")->find()["val"];//订单过期时间
        $conf["desc"]           =       C::where("key", "desc")->find()["val"];//网站描述
        $conf["is_tips"]        =       C::where("key", "is_tips")->find()["val"];//是否邮箱提醒
        $conf["pc_heart"]       =       C::where("key", "pc_heart")->find()["val"];//PC心跳时间
        $conf["pc_status"]      =       C::where("key", "pc_status")->find()["val"];//PC监控状态
        $conf["sitename"]       =       C::where("key", "sitename")->find()["val"];//网站名
        $conf["smtp_host"]      =       C::where("key", "smtp_host")->find()["val"];//邮箱主机
        $conf["smtp_pass"]      =       C::where("key", "smtp_pass")->find()["val"];//邮箱密码
        $conf["smtp_port"]      =       C::where("key", "smtp_port")->find()["val"];//邮箱端口
        $conf["smtp_user"]      =       C::where("key", "smtp_user")->find()["val"];//邮箱账号
        $conf["tips"]           =       C::where("key", "tips")->find()["val"];//支付页面提示
        $conf["yuyin"]          =       C::where("key", "yuyin")->find()["val"];//语音提示
        return $conf;
    }

    /**
     * 通道列表
     *
     * @return D[]|array|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function channellist()
    {
        $list   =   D::order("id", "desc")->paginate([ "list_rows" => 5]);
        $page   =   $list->render();
        return ["l" => $list, "p" => $page];
    }

    /**
     * 获取用户信息
     *
     * @return array
     */
    private function u()
    {
        if(Session::has("username")) return A::where("username", Session::get("username"))->find();
    }

    /**
     * 统计信息
     *
     * @return array
     * @throws \think\db\exception\DbException
     */
    private function census()
    {
        $today = strtotime(date("Y-m-d"), time());
        //查找创建时间为今日的订单
        $todayOrder = O::where("create_time >=" . $today)
                       ->where("create_time <=" . ($today + 86400))
                       ->count();
        //查询创建时间为今日并且已支付的订单的money字段相加
        $todayMoney = O::where("create_time >=" . $today)
                       ->where("create_time <=" . ($today + 86400))
                       ->where("status", "=", 1)
                       ->sum("money");
        //查询订单表的长度
        $countOrder = O::count();
        //查询所有已支付订单的money字段相加
        $countMoney = O::where("status", "=", 1)
                       ->sum("money");
        return [
            "todayorder" => $todayOrder,
            "todaymoney" => $todayMoney,
            "countorder" => $countOrder,
            "countmoney" => $countMoney
        ];
    }

    /**
     * 获取系统信息
     *
     * @return array
     */
    private function server_info()
    {
        //GD库信息
        if (function_exists("gd_info")) {
            $gd_info = @gd_info();
            $gd = $gd_info["GD Version"];
        } else {
            $gd = "GD库未开启!";
        }
        //MySQL版本
        $mysql = Db::query("SELECT VERSION();");
        $mysql = $mysql[0]['VERSION()'];
        //系统信息
        return [
            "程序版本"      => APP_VERSION,
            "GitHub"       => base64_decode(AUTHOR),
            "操作系统"      => PHP_OS,
            "服务器引擎"    => $_SERVER['SERVER_SOFTWARE'],
            "服务域名"      => Request::domain(),
            "PHP版本"      => PHP_VERSION,
            "MySQL版本"    => $mysql,
            "GD库版本"      => $gd,
        ];
    }

    /**
     * 异步回调方法
     *
     * @param $data
     * @param $key
     *
     * @return array
     */
    protected function create_call($data, $key)
    {
        $sign = md5("money=".$data['money']."&name=".$data['name']."&out_trade_no=".$data['out_trade_no']."&pid=". $data["pid"] . "&trade_no=".$data['trade_no']."&trade_status=TRADE_SUCCESS&type=".$data['type'].$key);
        $array = array('pid'=>$data["pid"],'trade_no'=>$data['trade_no'],'out_trade_no'=>$data['out_trade_no'],'type'=>$data['type'],'name'=>$data['name'],'money'=>$data['money'],'trade_status'=>'TRADE_SUCCESS');
        $urlstr = http_build_query($array);
        $url = [];
        if(strpos($data['notify_url'],'?')) {
            $url['notify']=$data['notify_url'].'&'.$urlstr.'&sign='.$sign.'&sign_type=MD5';
        } else {
            $url['notify']=$data['notify_url'].'?'.$urlstr.'&sign='.$sign.'&sign_type=MD5';
        }
        if(strpos($data['return_url'],'?')) {
            $url['return']=$data['return_url'].'&'.$urlstr.'&sign='.$sign.'&sign_type=MD5';
        } else {
            $url['return']=$data['return_url'].'?'.$urlstr.'&sign='.$sign.'&sign_type=MD5';
        }
        return $url;
    }

    /**
     * 发送邮件方法
     *
     * @param $title
     *
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function mail($title)
    {
        $dsn = "smtp://" . $this->data["c"]["smtp_user"] . ":" . $this->data["c"]["smtp_pass"] . "@" . $this->data["c"]["smtp_host"] . ":" . $this->data["c"]["smtp_port"];
        Conf::set([
            "host"      => $this->data["c"]["smtp_host"],
            "port"      => $this->data["c"]["smtp_port"],
            "username"  => $this->data["c"]["smtp_user"],
            "password"  => $this->data["c"]["smtp_pass"],
            "dsn"       => $dsn,
        ], "mailer");

        $mail = A::where("id", 1)->find()["email"];
        $mailer = new M();
        $mailer->from($this->data["c"]["smtp_user"]);//发件人邮箱
        $mailer->to($mail);//收件人邮箱
        $mailer->subject($title);//邮件主题
        $data = [
            "time"      => date("Y-m-d H:i:s"),
            "sitename"  => $this->data["c"]["sitename"],
            "title"     => $title
        ];
        $mailer->view("index/temp", $data);
        $mailer->send();
    }
}