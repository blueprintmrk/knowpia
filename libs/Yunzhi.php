<?php

namespace tools;

class Yunzhi
{
    private $config = [
        'SecretId'      => 'AKIDTKVsQAeAPrXRWlxFRpNqSD8DS2biecm6',
        'SecretKey'     => 'y9TzeTTel8hSOTjAkXXi3YC5BRsRA4AQ',
        'RequestMethod' => 'POST',
        'DefaultRegion' => 'gz',
    ];
    private $weizhi;
    public function __construct($config = [], $driver = '', $driverConfig = null)
    {
        require_once '../libs/QcloudApi/QcloudApi.php';
        $this->wenzhi = QcloudApi::load(QcloudApi::MODULE_WENZHI, $this->config);
    }

    /**
     * 关键词条提取
     */
    public function textkeywords($title = '', $content = '', $channel = '')
    {
        $package = ["title" => $title, "channel" => $channel, "content" => $content];
        $list    = $this->wenzhi->TextKeywords($package);
        if ($list === false) {
            return false;
        } else {
            if (is_array($list)) {
                $list = (array_filter($list['keywords']));
                if (count($list)) {
                    return $list;
                } else {
                    return false;
                }
            }
        }
    }

    /**敏感信息识别    Array ( [sensitive] => 0.73105857863 [nonsensitive] => 0.26894142137 )  **/
    public function textsensitivity($content, $type = 2)
    {
        $package = array("content" => $content, "type" => $type);
        $list    = $this->wenzhi->TextSensitivity($package);
        if ($list === false) {
            return false;
        } else {
            return $list;
        }
    }

    /**情感分析 正面情感概率  负面情感概率  Array ( [positive] => 0.98664039373398 [negative] => 0.013359599746764 ) **/
    public function textsentiment($content)
    {
        $package = array("content" => $content);
        $list    = $this->wenzhi->TextSentiment($package);
        if ($list === false) {
            return false;
        } else {
            return $list;
        }
    }

    /**分词&命名实体**/
    public function lexicalanalysis($content)
    {
        $package = array("content" => $content);
        $list    = $this->wenzhi->LexicalAnalysis($package);
        if ($list === false) {
            return false;
        } else {
            if (is_array($list)) {
                $list = (array_filter($list['keywords']));
                if (count($list)) {
                    return $list;
                } else {
                    return false;
                }
            }
        }
    }
}
