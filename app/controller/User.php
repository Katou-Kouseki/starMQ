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
use think\facade\Session;
use think\Request;
use app\model\Admin as A;

class User extends Base
{
    protected $middleware = [Check::class => []];

    public function index()
    {
        return $this->fetch();//渲染视图
    }

    public function user()
    {
        return $this->fetch();//渲染视图
    }

    public function order()
    {
        return $this->fetch();//渲染视图
    }

    public function config()
    {
        return $this->fetch();//渲染视图
    }

    public function smtp()
    {
        return $this->fetch();//渲染视图
    }

    public function channel()
    {
        return $this->fetch();//渲染视图
    }

    public function log()
    {
        return $this->fetch();//渲染视图
    }

    public function jk()
    {
        return $this->fetch();//渲染视图
    }

    public function login(Request $request)
    {
        //如果不是POST
        if (!$request->isPost()) {
            //如果session存在
            if (Session::has("Admin_Login"))return redirect("/User/index");
            //session不存在就渲染视图
            $this->assign($this->conf());
            return $this->fetch();
        }
        $username   =   $request->post("username");//用户名
        $password   =   $request->post("password");//密码
        $captcha    =   $request->post("captcha");//验证码
        if (!captcha_check($captcha)) return $this->ResJson(["code" => 201, "msg" => "验证码错误!", "data" => NULL]);//如果验证码错误
        try {
            $result = validate(\app\validate\User::class)->check(["username" => $username, "password" => $password]);
            if ($result != true) return $this->ResJson(["code" => 201, "msg" => $result, "data" => NULL]);
        } catch (ValidateException $e) {
            return $this->ResJson(["code" => 201, "msg" => $e->getMessage(), "data" => NULL]);
        }
        $user_info = A::where("username", $username)->find();
        if(!$user_info){
            $this->writelog("登录(账号错误)", 0);
            return $this->ResJson(["code" => 201, "msg" => "账号或密码错误!", "data" => NULL]);
        }
        if(sha1($user_info["salt"] . $password) != $user_info["password"]){
            $this->writelog("登录(密码错误)", 0);
            return $this->ResJson(["code" => 201, "msg" => "账号或密码错误!", "data" => NULL]);
        }
        Session::set("Admin_Login", sha1($user_info));
        Session::set("username", $username);
        A::where("username", $username)->update([
            "token"         =>      token(),
            "login_ip"      =>      $request->ip(),
            "login_time"    =>      time()
        ]);
        $this->writelog("登录成功", 1);
        return $this->ResJson(["code" => 200, "msg" => "登录成功!", "data" => NULL]);
    }

    public function logout(Request $request)
    {
        if (!$request->isPost())return redirect("/User/index");
        Session::clear();
        $this->writelog("注销登录", 1);
        return $this->ResJson(["code" => 200, "msg" => "已注销登录!", "data" => NULL]);
    }

    public function cache(Request $request)
    {
        if (!$request->isPost()) return redirect("/User/index");
        $path = root_path() . "runtime";
        delFiles($path);
        $this->writelog("清除缓存", 1);
        return $this->ResJson(["code" => 200, "msg" => "清除成功!", "data" => NULL]);
    }

    public function uc(Request $request)
    {
        if (!$request->isPost()) return redirect("/User/index");
        $nickname   =   $request->post("nickname");//昵称
        $username   =   $request->post("username");//用户名
        $qq         =   $request->post("qq");//QQ
        $email      =   $request->post("email");//邮箱
        $password   =   $request->post("password");//密码
        $salt       =   $this->data["u"]["salt"];//密码盐
        $user       =   $this->data["u"]["username"];//当前用户名
        if (empty($password)){
            $update = [
                "nickname"  =>  $nickname,
                "username"  =>  $username,
                "email"     =>  $email,
                "qq"        =>  $qq
            ];
        }else{
            $update = [
                "nickname"  =>  $nickname,
                "username"  =>  $username,
                "email"     =>  $email,
                "qq"        =>  $qq,
                "password"  =>  sha1($salt.$password)
            ];
        }
        $res = A::where("username", $user)->update($update);
        if ($res) {
            $this->writelog("修改个人信息", 1);
            return $this->ResJson(["code" => 200, "msg" => "修改成功!", "data" => NULL]);
        }else{
            $this->writelog("修改个人信息", 0);
            return $this->ResJson(["code" => 201, "msg" => "修改失败!", "data" => NULL]);
        }
    }
}