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
 * 字符处理工具
 */
class Strings
{
    /**
     * 生成UUID 单机使用
     * @access public
     * @return string
     */
    public static function uuid()
    {
        $charid = self::keyGen();
        $hyphen = chr(45);
        $uuid   = chr(123)
        . substr($charid, 0, 8) . $hyphen
        . substr($charid, 8, 4) . $hyphen
        . substr($charid, 12, 4) . $hyphen
        . substr($charid, 16, 4) . $hyphen
        . substr($charid, 20, 12)
        . chr(125);
        return $uuid;
    }

    /**
     * 生成Guid主键
     * @return string
     */
    public static function keyGen()
    {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * 检查字符串是否是UTF8编码
     * @param  string $string 字符串
     * @return boolean
     */
    public static function isUtf8($str)
    {
        $c    = 0;
        $b    = 0;
        $bits = 0;
        $len  = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $c = ord($str[$i]);
            if ($c > 128) {
                if (($c >= 254)) {
                    return false;
                } elseif ($c >= 252) {
                    $bits = 6;
                } elseif ($c >= 248) {
                    $bits = 5;
                } elseif ($c >= 240) {
                    $bits = 4;
                } elseif ($c >= 224) {
                    $bits = 3;
                } elseif ($c >= 192) {
                    $bits = 2;
                } else {
                    return false;
                }
                if (($i + $bits) > $len) {
                    return false;
                }
                while ($bits > 1) {
                    $i++;
                    $b = ord($str[$i]);
                    if ($b < 128 || $b > 191) {
                        return false;
                    }
                    $bits--;
                }
            }
        }
        return true;
    }

    /**
     * 字符串截取，支持中文和其他编码
     * @param  string $str     需要转换的字符串
     * @param  string $start   开始位置
     * @param  string $length  截取长度
     * @param  string $charset 编码格式
     * @param  string $suffix  截断显示字符
     * @return string
     */
    public static function msubstr($str, $start, $length, $suffix = true, $charset = "utf-8")
    {
        if (mb_strlen($str, $charset) < $length) {
            return $str;
        }
        if (function_exists("mb_substr")) {
            $slice = mb_substr($str, $start, $length, $charset);
        } elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $charset);
        } else {
            $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice . '..' : $slice;
    }

    /**
     * 产生随机字串，可用来自动生成密码
     * 默认长度6位 字母和数字混合
     * @param  string $len 长度
     * @param  string $type 字串类型
     * 0 字母 1 数字 其它 混合
     * @param  string $addChars 额外字符
     * @return string
     */
    public static function randString($len = 6, $type = '', $addChars = '')
    {
        $str = '';
        switch ($type) {
            case 0:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 1:
                $chars = str_repeat('0123456789', 3);
                break;
            case 2:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
                break;
            case 3:
                $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            default:
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
                break;
        }
        if ($len > 10) {
            $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
        }
        $chars = str_shuffle($chars);
        $str   = substr($chars, 0, $len);
        return $str;
    }

    /**
     * 生成一定数量的随机数，并且不重复
     * @param  integer $number 数量
     * @param  integer $len    长度
     * @param  integer $type   字串类型
     * @param  integer $mode   0 字母 1 数字 其它 混合
     * @return string
     */
    public static function buildCountRand($number, $length = 4, $mode = 1)
    {
        $rand = [];
        for ($i = 0; $i < $number; $i++) {
            $rand[] = self::randString($length, $mode);
        }
        $unqiue = array_unique($rand);
        if (count($unqiue) == count($rand)) {
            return $rand;
        }
        $count = count($rand) - count($unqiue);
        for ($i = 0; $i < $count * 3; $i++) {
            $rand[] = self::randString($length, $mode);
        }
        $rand = array_slice(array_unique($rand), 0, $number);
        return $rand;
    }

    /**
     * 获取一定范围内的随机数字 位数不足补零
     * @param  integer $min 最小值
     * @param  integer $max 最大值
     * @return string
     */
    public static function randNumber($min, $max)
    {
        return sprintf("%0" . strlen($max) . "d", mt_rand($min, $max));
    }

    /**
     * 创建一个20位的数字订单号
     * @param  string $prefix 订单号前缀
     * @return string
     */
    public static function createOrderId($prefix = '')
    {
        $code = date('ymdHis') . self::randNumber(0, 99999999);
        if (!empty($prefix)) {
            $code = $prefix . substr($code, 0, 20 - strlen($prefix));
        }
        return $code;
    }

    /**
     * 短网址生成算法
     * @param   string $url 要计算的URL
     * @return string
     */
    public static function shortUrl($url)
    {
        $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $key     = "Uncle.Chen";
        $date    = microtime();
        $urlhash = md5($key . $url . $date);
        $len     = strlen($urlhash);
        for ($i = 0; $i < 4; $i++) {
            $urlhash_piece = substr($urlhash, $i * $len / 4, $len / 4);
            $hex           = hexdec($urlhash_piece) & 0x3fffffff;
            $short_url     = "";
            for ($j = 0; $j < 6; $j++) {
                $short_url .= $charset[$hex & 0x0000003d];
                $hex = $hex >> 5;
            }
            $short_url_list[] = $short_url;
        }
        $ret = rand(0, 3);
        return $short_url_list[$ret];
    }
}
