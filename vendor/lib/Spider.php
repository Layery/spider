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


    public function __construct()
    {
        if (!$this->_ch) {
            $this->_ch = curl_init();
        }
    }

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

    public function setCookie($dir = '', $prefix = 'cookie')
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, 1);
        }
        $tempFile = $dir. DIRECTORY_SEPARATOR . $prefix .  '.tmp';
        if (!is_file($tempFile)) {
            exec('type null > '. $tempFile);
        }
        $this->cookie = file_get_contents($tempFile);
        //$this->cookie = 'pgv_pvi=9154761728; _qddaz=QD.5f6pu3.cm59sg.jtgzxedr; tencentSig=8879076352; XSRF-TOKEN=eyJpdiI6IkNCTFwvMnRHOFgrSGdKamtTUHdXbURRPT0iLCJ2YWx1ZSI6IlllcDlzU1ZEdnM0dGxFQVAxNkpzUVhvd0h4dmo5VzliT0JWbjlTdkJJbW9lZDIzQWdJMzArNnU5cWtMZDFrTTAiLCJtYWMiOiJjZjUzYTM0OWFjYmE2NzI4MmZlNzI3OTY1NzIyYzBlODJlYjExY2YwODNmN2M0YTcyNWRlMDM5ZDM1ODY3YTYwIn0%3D; laravel_session=eyJpdiI6ImFCWG4zQWR4ZDZ6dnFGdFZVUGl6R1E9PSIsInZhbHVlIjoiK0pnZkU0T3pHTjhDV0FsRG5pQ25sUXErV3pRWEVpVWxDWWFmdG82T0E0Qm9TXC9NcUoyVHJHRmFwTXdWeUJadVgiLCJtYWMiOiI0YmMyYzVjMDQ2MzM1NjFiOWYyZTJhMDFlMmRmMzY1MDhiY2QyNzg3OGRkNmNmN2E4MDVjOWJjMDIyOTJlNTVmIn0%3D';
        if ($this->cookie) {
            curl_setopt($this->_ch, CURLOPT_COOKIE, 'pgv_pvi=9154761728; _qddaz=QD.5f6pu3.cm59sg.jtgzxedr; tencentSig=8879076352; XSRF-TOKEN=eyJpdiI6IkNCTFwvMnRHOFgrSGdKamtTUHdXbURRPT0iLCJ2YWx1ZSI6IlllcDlzU1ZEdnM0dGxFQVAxNkpzUVhvd0h4dmo5VzliT0JWbjlTdkJJbW9lZDIzQWdJMzArNnU5cWtMZDFrTTAiLCJtYWMiOiJjZjUzYTM0OWFjYmE2NzI4MmZlNzI3OTY1NzIyYzBlODJlYjExY2YwODNmN2M0YTcyNWRlMDM5ZDM1ODY3YTYwIn0%3D; laravel_session=eyJpdiI6ImFCWG4zQWR4ZDZ6dnFGdFZVUGl6R1E9PSIsInZhbHVlIjoiK0pnZkU0T3pHTjhDV0FsRG5pQ25sUXErV3pRWEVpVWxDWWFmdG82T0E0Qm9TXC9NcUoyVHJHRmFwTXdWeUJadVgiLCJtYWMiOiI0YmMyYzVjMDQ2MzM1NjFiOWYyZTJhMDFlMmRmMzY1MDhiY2QyNzg3OGRkNmNmN2E4MDVjOWJjMDIyOTJlNTVmIn0%3D');
        } else {
            curl_setopt($this->_ch, CURLOPT_COOKIEFILE, $tempFile);
            curl_setopt($this->_ch, CURLOPT_COOKIEJAR, $tempFile); //存储提交后得到的cookie数据
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
        curl_setopt($this->_ch,CURLOPT_SSL_VERIFYHOST, 0); // 跳过证书验证
        #curl_setopt($this->_ch, CURLOPT_SSLVERSION, 2); // ?todo what?
        return $this;
    }

    public function get($url = '', $data = [])
    {
        return call_user_func([$this, 'curl'], $url, $data);
    }

    public function post($url = '', $data = [], $timeOut = 30)
    {
        curl_setopt($this->_ch, CURLOPT_POST, 1);
        return call_user_func([$this, 'curl'], $url, $data, $timeOut);
    }

    public function download($url = '')
    {
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($this->_ch, CURLOPT_HTTPGET, 1); // 获取的信息以文件流的形式返回
        curl_setopt($this->_ch,CURLOPT_CONNECTTIMEOUT,30);
        curl_setopt($this->_ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
        curl_setopt($this->_ch, CURLOPT_DNS_CACHE_TIMEOUT, 2 );
        curl_setopt($this->_ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        return call_user_func([$this, 'curl'], $url);
    }

    protected function curl($url = '', $data = [], $timeOut = 30)
    {
        $result = NULL;
        if ($url) {
            curl_setopt($this->_ch, CURLOPT_HEADER, false); // 设置头文件的信息作为数据流输出
            curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true); // 返回文件流形式而不直接输出
            #curl_setopt($this->_ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            #curl_setopt($this->_ch, CURLOPT_HTTPHEADER, array('Expect:'));
            #curl_setopt($this->_ch, CURLOPT_PROXY, '127.0.0.1:8888');
            curl_setopt($this->_ch,CURLOPT_CONNECTTIMEOUT, $timeOut);
            curl_setopt($this->_ch, CURLOPT_URL, $url);
            if ($data) {
                $dataStr = http_build_query($data);
                curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $dataStr);
            }
            $result = curl_exec($this->_ch);
            if (curl_errno($this->_ch)) {
                throw new \Exception(curl_error($this->_ch));
            }
        } else {

        }
        if ($this->convertCharset) {
            $result = mb_convert_encoding($result, $this->convertCharset, 'GBK');
        }
        return $result;
    }

    public function __destruct()
    {
        curl_close($this->_ch);
    }
}

