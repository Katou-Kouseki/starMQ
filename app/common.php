<?php
/**
 * 作者: 深秋
 * QQ : 1361582519
 * 官方QQ群: 758107405
 * GitHub: https://github.com/kaindev8/starMQ
 * 保留版权信息，尊重开源精神!
 * 禁止修改此文件!
 */
// 应用公共文件
function delFiles($dir): void
{
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {
            $fullpath = $dir . "/" . $file;
            if (is_dir($fullpath)) {
                delFiles($fullpath);
            } else {
                unlink($fullpath);
            }
        }
    }
    closedir($dh);
}

function addres(string $ip)
{
    $response = \star\Http::get("https://sp1.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?query={$ip}&resource_id=5809&tn=baidu");
    $response = json_decode($response, true);
    return $response["data"][0]["location"];
}