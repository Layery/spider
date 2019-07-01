<?php

/**
 * Created by PhpStorm.
 * User: llf
 * Date: 2018/12/25
 * Time: 13:34
 */
class Spider
{
    protected $_ch;

    protected $url;

    protected $header = [
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
        #'Accept-Encoding' => 'gzip, deflate, br', // 发送编码之后的数据
        'Accept-Language' => 'zh-CN,zh;q=0.9,und;q=0.8',
        'Cache-Control' => 'no-cache',
        'Pragma' => 'no-cache',
        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
        #'upgrade-insecure-requests' => '1',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.67 Safari/537.36',
    ];

    protected $option = [];

    protected $cookie;

    protected $followLocation = false;

    protected $convertCharset;

    protected $xSrfToken;

    public function __construct()
    {
        if (!$this->_ch) {
            $this->_ch = curl_init();
        }
    }

    /**
     * @param string $params
     * @return self
     */
    public function setUrl($params = '')
    {
        $this->url = $params;
        return $this;
    }

    /**
     * @param string $charset
     * @return self
     */
    public function setReturnCharset($charset = 'UTF-8')
    {
        if ($charset) {
            $this->convertCharset = $charset;
        }
        return $this;
    }

    /**
     * @param array $params
     * @return self
     */
    public function setHeader($params = [])
    {
        $header = $opt = [];
        if (!empty($params)) {
            foreach ($params as $k => $v) {
                $header[$k] = $v;
            }
            $this->header = array_merge($this->header, $header);
        }
        foreach ($this->header as $key => $val) {
            $opt[] = $key . ":" . $val;
        }
        $this->header = $opt;
        curl_setopt($this->_ch, CURLOPT_HTTPHEADER, $opt);
        return $this;
    }

    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return self
     */
    public function setXsrfToken()
    {
        $ch = curl_init();
        if (strpos($this->url, 'https') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书验证
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 跳过证书验证
        }
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);
        preg_match('/\<meta\s+name=\"csrf-token\".*?content=\"(.*?)\"\>/is', $content, $meta);
        $this->xSrfToken = $meta[1];
        return $this;
    }

    /**
     * @param string $file
     * @return self
     */
    public function setCookie($file = '')
    {
        if ($file) {
            $this->cookie = 'XSRF-TOKEN=eyJpdiI6InJMRDBUQ1N1MEQ4cDZ3bytYaWtibmc9PSIsInZhbHVlIjoid25vYWRpUWJwZkw3Yks2VHFTZVBJYmVzWGNIbEF0SFlyMW41UlBDVWZLeFZqNjFWeW85Rkk2RnNaZ0F4Ym5kNSIsIm1hYyI6IjhmZjZkM2IzNGViZTRiODA0NmVkNDI5ZTYxY2YwZDRlYmFhYzUwMjdlNjZkMmYxM2MzMTUwZjAyMzNmYjUzN2EifQ%3D%3D';
            $this->cookie = 'XSRF-TOKEN=eyJpdiI6IlFVaXE0bkpoQW1pS2hQSnM1ekF1U3c9PSIsInZhbHVlIjoiSFJ5bGhrdHpxaURtTTBQN3k4bHVId3hPUjN5cU1MeWM2VkUxRFUwUlJWNGM3RkhkdkZVRUNmc050VVBRYk1MXC8iLCJtYWMiOiJlMzcyZThmNDNjZDZiMTEzZmM2NzJjMDdhNjUxMGQ1Zjc1MGVjMDFmYWZiN2U3NGY5N2MxNzAzZmVhZjJlY2ZiIn0%3D; laravel_session=eyJpdiI6ImNuaGF3VSt2eVBUNVBIN3lmMXFjaVE9PSIsInZhbHVlIjoiXC95M1VqbDdaZUNvSVNVcjdaQTBPM2tcL1B1bEJSVHVmdElsN3R0cmNycG1EcEFoT2NNMGtBQndOVUVSemVFZ2plIiwibWFjIjoiYTA3ZTI5MzcwZDA5ZTM4MGU1NWM0ZTFmNmI1MjUzNjBjNDE5ZmVkNzVhOTIxMzk4YmQ2MzYxZDZhYmE2OTE1NiJ9';
            $this->cookie = 'IESESSION=alive; pgv_pvi=3402590208; pgv_si=s127086592; tencentSig=1516524544; _qddamta_4006660033=2-0; _qddaz=QD.slkhqk.dcwcan.jvghkdc9; XSRF-TOKEN=eyJpdiI6InlBTUZNM094SW5sekJCc0JwQ3BCT2c9PSIsInZhbHVlIjoiUVdBWTR4bk1jQ3RhZUpkcW40cGdpXC83NWRHWWU1Sk1WaVZzN0JtWmVBbzBzVFZSc2d4QmVZWEJ3a1BkYlk4eloiLCJtYWMiOiI1MWI4MDM5MzA5MGRlNWUwYWU3ZWQyNDg2MDEzNmM5NGYxOTU4ZjE0ODA5ODljMTU2N2IyZTJjYTQ1NDBjZTVhIn0%3D; laravel_session=eyJpdiI6IkF5azhCT1UzTUxzalRFMTNcL1wvY04xUT09IiwidmFsdWUiOiJkSWFvb2tYOFJBZllrdDZQOTA4VHJGY2tvVUpQWFwvSkFuUUlLTnZcL0tiTlJkbEk0eEpINHQ2cWFxbkE4MTdyR3ciLCJtYWMiOiJkYWEzNGUwODBmNzg2OWM1OThhOGIyYTk4YTlkNDk5MTA0MDY3YTc3NmM5ZDczOWMyNTM4NjdlNmVmNGMyZTY5In0%3D; _qdda=2-1.1; _qddab=2-el5k5n.jvghkdcb';
            curl_setopt($this->_ch, CURLOPT_COOKIE, $this->cookie);
        }
        return $this;
    }

    public function setPort($port = '80')
    {
        curl_setopt($this->_ch, CURLOPT_PORT, $port);
        return $this;
    }


    /**
     * 设置不验证ssl
     *
     * @return $this
     */
    public function setUnCheckSsl()
    {
        curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书验证
        curl_setopt($this->_ch, CURLOPT_SSL_VERIFYHOST, 0); // 跳过证书验证
        #curl_setopt($this->_ch, CURLOPT_SSLVERSION, 2); // ?todo what?
        return $this;
    }

    public function get($data = [], $timeOut = 30)
    {
        return call_user_func([$this, 'curl'], $data, $timeOut);
    }

    public function post($data = [], $timeOut = 30)
    {
        curl_setopt($this->_ch, CURLOPT_POST, 1);
        return call_user_func([$this, 'curl'], $data, $timeOut);
    }

    public function download_bak($fileName = '', $timeOut = 0)
    {
        set_time_limit($timeOut); // 设置超时时间
        $path = pathinfo($fileName);
        if (!is_dir($path['dirname'])) {
            mkdir($path['dirname'], 0777, 1);
        }
        try {
            $fp = fopen($fileName, 'w+');
            curl_setopt($this->_ch, CURLOPT_URL, $this->url);
            if (strpos($this->url, 'https') !== false) {
                curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书验证
                curl_setopt($this->_ch, CURLOPT_SSL_VERIFYHOST, 0); // 跳过证书验证
            }
    //            curl_setopt($this->_ch, CURLOPT_RANGE, '300');
            curl_setopt($this->_ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true); // 返回文件流形式而不直接输出
            curl_setopt($this->_ch, CURLOPT_FILE, $fp);
            $data = curl_exec($this->_ch);
            fclose($fp);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return true;
    }


    /**
     * download and save files
     *
     * @param string $fileName
     * @param string $timeout
     * @return mixed
     */
    public function download($fileName = '', $timeOut = 0)
    {
        set_time_limit($timeOut); // 设置超时时间
        ini_set('memory_limit', '1048M');
        $path = pathinfo($fileName);
        if (!is_dir($path['dirname'])) {
            mkdir($path['dirname'], 0777, 1);
        }
        try {
            $source = fopen($this->url, "rb"); // 远程下载文件，二进制模式
            #$head = get_headers($this->url, true);
            if ($source) { // 如果下载成功
                $fp = fopen($fileName, "wb"); // 打开本地的一个句柄, 如果没有则生成
                if ($fp) {
                    while (!feof($source)) { // 判断附件写入是否完整
                        fwrite($fp, fread($source, 1024*10), 1024*10); // 没有写完就继续
                    }
                }
                fclose($source); // 关闭远程文件
                fclose($fp); // 关闭本地文件
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return true;
    }

    protected function getCookie($url = '', $post = [])
    {
        $ch = curl_init();
        if (strpos($url, 'https') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书验证
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 跳过证书验证
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * 获取curl结果, 配合curl方法可无限次跳转抓取
     *
     * @return bool|Exception/mixed
     */
    protected function getCurlResult()
    {
        $result = curl_exec($this->_ch);
        if (curl_errno($this->_ch)) {
            $error = curl_error($this->_ch);
            return new Exception($error);
        }
        $curlInfo = curl_getinfo($this->_ch);
        switch ($curlInfo['http_code']) {
            case 200:
                preg_match('/\<meta.*?http-equiv=[\'"]refresh[\'"].*?(content=[\'"].*?[\'"])\>/is', $result, $m);
                if (!empty($m) && $m[0]) { // 存在refresh
                    preg_match('/(?<=[\'\"]).*?(?=[\'\"])/is', $m[1], $refreshUrl);
                    $refreshUrl = explode('=', $refreshUrl[0]);
                    $refreshUrl = $refreshUrl[1];
                    $host = pathinfo($this->url);
                    $refreshUrl = $host['dirname'] . '/' . $refreshUrl;
                    $result = $this->setUrl($refreshUrl)->setHeader()->curl();
                }
                break;
            case 302 || 301:
                $redirectUrl = $curlInfo['redirect_url'];
                $result = $this->setUrl($redirectUrl)->setHeader()->curl();
                break;
            case 404:
                $result = 'has null page result, http code 404';
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * 开启自动跟踪页面跳转
     *
     * @return self
     */
    public function setFollowLocation()
    {
        $this->followLocation = true;
        return $this;
    }

    protected function curl($data = [], $timeOut = 30)
    {
        $result = NULL;
        if ($this->url) {
            if (strpos($this->url, 'https') !== false) {
                curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书验证
                curl_setopt($this->_ch, CURLOPT_SSL_VERIFYHOST, 0); // 跳过证书验证
            }
            curl_setopt($this->_ch, CURLOPT_HEADER, false); // 设置头文件的信息作为数据流输出
            curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true); // 返回文件流形式而不直接输出
            #curl_setopt($this->_ch, CURLOPT_HTTPHEADER, array('Expect:'));
            #curl_setopt($this->_ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
            #curl_setopt($this->_ch, CURLOPT_PROXY, '117.90.3.36:9000'); // fiddler debug
            curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, $timeOut);
            curl_setopt($this->_ch, CURLOPT_URL, $this->url);
//            curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, true); // 自动追踪302跳转

            if ($data) {
                $dataStr = http_build_query($data);
                curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $dataStr);
            }
            if ($this->followLocation) {
                $result = $this->getCurlResult();
            } else {
                $result = curl_exec($this->_ch);
            }
        } else {
            return "url can not be null";
        }
        if ($this->convertCharset) {
            $result = mb_convert_encoding($result, $this->convertCharset);
        }
        return $result;
    }

    public function __destruct()
    {
        curl_close($this->_ch);
    }
}

