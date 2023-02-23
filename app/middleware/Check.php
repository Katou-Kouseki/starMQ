<?php
/**
 * 作者: 深秋
 * QQ : 1361582519
 * 官方QQ群: 758107405
 * GitHub: https://github.com/kaindev8/starMQ
 * 保留版权信息，尊重开源精神!
 * 禁止修改此文件!
 */
namespace app\middleware;
use think\facade\Session;

class Check
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     *
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        //判断登录状态
        //如果session不存在 && login不在pathinfo里
        if (!Session::has('Admin_Login') && !preg_match('/login/', $request->pathinfo())) {
            return redirect((string)url("/User/login/"));
        }
        return $next($request);
    }
}