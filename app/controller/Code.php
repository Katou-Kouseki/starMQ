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
use app\Request;
use app\model\Code as D;
use star\Http;
use \think\facade\Filesystem as F;

class Code extends Base
{
    protected $middleware = [Check::class => []];

    /**
     * 改变通道状态
     *
     * @param Request $request
     *
     * @return \think\Response|\think\response\Redirect
     */
    public function change(Request $request)
    {
        if (!$request->isPost()) return redirect("/User/index");
        $data = $request->post();
        $res = D::where("id", $data["id"])->update(["status" => $data["status"]]);
        if ($res){
            return $this->ResJson(["code" => 200, "msg" => "更新成功!", "data" => NULL]);
        }else{
            return $this->ResJson(["code" => 201, "msg" => "更新失败!", "data" => NULL]);
        }
    }

    /**
     * 新增通道
     *
     * @param Request $request
     *
     * @return \think\Response|void
     */
    public function add(Request $request)
    {
        $type   = $request->post("type");
        $jk     = $request->post("jk");
        $file   = $request->file("imgcode");
        $code   = $request->post("code");
        $D      = new D();
        if (empty($code)) {
            $save = F::disk('public')
                     ->putFile('upload', $file);
            $path = $request->domain() . "/" . $save; //二维码网络路径
            $code = $this->deqr($path);
            unlink(root_path() . "public" . DS . $save); //删除文件
            if (!$code) {
                return $this->ResJson(["code" => 201, "msg" => "解析失败,请手动添加!", "data" => NULL]);
            }
            $res = $D->save(["url" => $code, "type" => $type, "jk" => $jk, "time" => time(), "status" => 1]);
        }
        $res = $D->save(["url" => $code, "type" => $type, "jk" => $jk, "time" => time(), "status" => 1]);
        if ($res){
            $this->writelog("新增通道", 1);
            return $this->ResJson(["code" => 200, "msg" => "新增成功!", "data" => NULL]);
        }else{
            $this->writelog("新增通道", 0);
            return $this->ResJson(["code" => 201, "msg" => "新增失败!", "data" => NULL]);
        }
    }


    /**
     * 删除通道
     *
     * @param Request $request
     *
     * @return \think\Response|\think\response\Redirect
     */
    public function delete(Request $request)
    {
        if (!$request->post()) return redirect("/User/index");
        $id = $request->post("id");
        $res = D::destroy($id);
        if ($res){
            $this->writelog("删除通道", 1);
            return $this->ResJson(["code" => 200, "msg" => "删除成功!", "data" => NULL]);
        }else{
            $this->writelog("删除通道", 0);
            return $this->ResJson(["code" => 201, "msg" => "删除失败!", "data" => NULL]);
        }
    }

    /**
     * 解码接口
     *
     * @param $path
     *
     * @return false|mixed
     */
    private function deqr($path)
    {
        $res = json_decode(Http::get("https://cli.im/Api/Browser/deqr?data=" . $path), true);
        if ($res == NULL) return false;
        if ($res["status"] == 1){
            return $res["data"]["RawData"];
        }else{
            return false;
        }
    }
}