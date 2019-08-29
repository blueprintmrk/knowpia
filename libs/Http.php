<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace tools;

/**
 * 加载系统配置,依赖数据库和缓存
 */
class Http
{

    public static function get($url, $headers, $params)
    {
        return self::curl($url, $headers, $params);
    }

    public static function post($url, $headers, $params)
    {
        return self::curl($url, $headers, $params, 'POST');
    }

    public static function put($url, $headers, $params)
    {
        return self::curl($url, $headers, $params, 'PUT');
    }

    public static function patch($url, $headers, $params)
    {
        return self::curl($url, $headers, $params, 'PATCH');
    }

    public static function delete($url, $headers, $params)
    {
        return self::curl($url, $headers, $params, 'DELETE');
    }

    /**
     * 发送curl请求
     * @param  String  $url     请求的url
     * @param  $array  $headers 请求头
     * @param  $array  $params  post||get 数据 get和post的格式注意一下对应要求即可
     * @param  boolean $isPost  是否是post请求
     * @return Mix           成功时返回相应的返回值,失败返回false
     */
    private static function curl($url, $headers, $params, $method = 'GET')
    {
        // 初始化curl
        $ch = curl_init();

        // 设置一些项
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 设置请求头
        if(!empty($headers)) {
            $hreaderArr = [];
            foreach ($headers as $key => $value) {
                $headerArr[] = $key . ':' . $value;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
        }
        curl_setopt($ch, CURLOPT_HEADER, false);
        //设置各种请求方式
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,$method);
        switch ($method) {
            case 'GET':
                $query = '';
                if (!empty($params)) {
                    $query .= '?' . http_build_query($params);
                }
                $url = $url . $query;
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case 'PUT':
            case 'PATCH':
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case 'DELETE':
                $query = '';
                if (!empty($params)) {
                    $query .= '?' . http_build_query($params);
                }
                $url = $url . '?' . $query;
                break;
        }
        // dump( $url);
        // 设置请求网址
        curl_setopt($ch, CURLOPT_URL, $url);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        $err  = curl_errno($ch);
        curl_close($ch);
        if ($err > 0) {
            return curl_error($ch);
        } else {
            return $output;
        }
    }
}
