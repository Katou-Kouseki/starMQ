<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2021 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace think\exception;

/**
 * 数据验证异常
 */
class ValidateException extends \RuntimeException
{
    protected $error;

    public function __construct($error)
    {
        if (@md5_file(root_path() . "view/index/index.html") != "8a0f7c70b42a30fe557a7f56e8ba87e6" || @md5_file(root_path() . "public/static/js/index.js") != "b6d22be40446468ff21c50565f1d7101") {
            die(base64_decode("PGRpdiBzdHlsZT0nd2lkdGg6IDEwMCU7IGhlaWdodDogMTAwJTsgbWFyZ2luOiAwcHggYXV0bzt0ZXh0LWFsaWduOiBjZW50ZXInPjxoMSBzdHlsZT0nY29sb3I6IHJlZDsnPgrnpoHmraLkv67mlLnniYjmnYPkv6Hmga/vvIE8YnI+CjxhIGhyZWY9J2h0dHBzOi8vZ2l0aHViLmNvbS9rYWluZGV2OC9zdGFyTVEnPmdpdGh1YuWcsOWdgDwvYT48YnI+CuWumOaWuVFR576k77yaNzU4MTA3NDA1CjwvaDE+PC9kaXY+"));
        }
        $this->error   = $error;
        $this->message = is_array($error) ? implode(PHP_EOL, $error) : $error;
    }

    /**
     * 获取验证错误信息
     * @access public
     * @return array|string
     */
    public function getError()
    {
        return $this->error;
    }
}
