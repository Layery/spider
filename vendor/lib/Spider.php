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
     * @param string $dir asdf
     * @param string $prefix
     * @return self
     */
    public function setCookie($dir = '', $prefix = 'cookie')
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, 1);
        }
        $tempFile = $dir . DIRECTORY_SEPARATOR . $prefix . '.tmp';
        if (!is_file($tempFile)) {
            exec('type null > ' . $tempFile);
        }
        $this->cookie = $tempFile;
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

    public function get($data = [])
    {
        return call_user_func([$this, 'curl'], $data);
    }

    public function post($data = [], $timeOut = 30)
    {
        curl_setopt($this->_ch, CURLOPT_POST, 1);
        return call_user_func([$this, 'curl'], $data, $timeOut);
    }

    public function download()
    {
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($this->_ch, CURLOPT_HTTPGET, 1); // 获取的信息以文件流的形式返回
        curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($this->_ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($this->_ch, CURLOPT_DNS_CACHE_TIMEOUT, 2);
        curl_setopt($this->_ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        return call_user_func([$this, 'curl'], []);
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

    protected function curl($data = [], $timeOut = 30)
    {
        $result = NULL;
        if ($this->url) {
            curl_setopt($this->_ch, CURLOPT_HEADER, false); // 设置头文件的信息作为数据流输出
            curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true); // 返回文件流形式而不直接输出
            #curl_setopt($this->_ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            #curl_setopt($this->_ch, CURLOPT_HTTPHEADER, array('Expect:'));
//            curl_setopt($this->_ch, CURLOPT_PROXY, '127.0.0.1:8888'); // fiddler debug
            curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, $timeOut);
            curl_setopt($this->_ch, CURLOPT_URL, $this->url);

            if ($this->cookie) {
                if (!filesize($this->cookie)) {
                    $this->getCookie($this->url, $data);
                }
                curl_setopt($this->_ch, CURLOPT_COOKIEFILE, $this->cookie);
            }
            if ($this->xSrfToken) {
                $this->header[] = 'X-CSRF-TOKEN:cfIHBvog8OBLsyUnwWDkS5UaI3sgb3cZucL5lGtA'; //. $this->xSrfToken;
                curl_setopt($this->_ch, CURLOPT_HTTPHEADER, $this->header);
            }
            if ($data) {
                $dataStr = http_build_query($data);
                curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $dataStr);
            }
            $result = curl_exec($this->_ch);
            if (curl_errno($this->_ch)) {
                return new \Exception(curl_error($this->_ch));
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

