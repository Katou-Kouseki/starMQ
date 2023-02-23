<?php
/**
 * 作者: 深秋
 * QQ : 1361582519
 * 官方QQ群: 758107405
 * GitHub: https://github.com/kaindev8/starMQ
 * 保留版权信息，尊重开源精神!
 * 禁止修改此文件!
 */
declare (strict_types=1);

namespace app;

use think\App;
use think\exception\ValidateException;
use think\facade\View;
use think\Response;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     *
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
    }

    /**
     * 验证数据
     * @access protected
     *
     * @param array        $data     数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array        $message  提示信息
     * @param bool         $batch    是否批量验证
     *
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)
                 ->check($data);
    }

    /**
     * 页面变量
     *
     * @param array $data
     *
     * @return mixed
     */
    protected function assign(array $data)
    {
        return View::assign($data);
    }

    /**
     * 页面渲染
     *
     * @param $template
     * @param $data
     *
     * @return mixed
     */
    protected function fetch($template = '', $data = [])
    {
        return View::fetch($template, $data);
    }

    /**
     * 数据返回
     *
     * @param $data array 要返回的数据
     * @param $type string 输出格式 json|html
     * @param $code int 响应码
     *
     * @return Response
     */
    protected function ResJson(array $data = [], string $type = "json", int $code = 200)
    {
        return Response::create($data, $type, $code);
    }

}
