<?php
/**
 * Created by PhpStorm.
 * User: llf
 * Date: 2018/12/29
 * Time: 10:05
 */

require './config/config.php';
require './vendor/autoload.php';
require './vendor/lib/Spider.php';
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\FileCookieJar;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;



class PostServer
{
    private $serverLoginApi = 'http://api.abeiyun.com/www/login.php';


    private $serverCheckEndTimeApi = 'http://api.abeiyun.com/www/vps.php';

    private $serverReNewApi = 'http://api.abeiyun.com/www/renew.php';

    private $postTieBaApi = 'http://tieba.baidu.com/f/commit/thread/add';

    public $testBaiDuApi = 'http://tieba.baidu.com/sysmsg/query/userunread';

    public  $headers = [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36 Edg/80.0.361.69',
//        'Content-Type' => 'application/json; charset=UTF-8',
        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
    ];

    public $baiduHeaders = [
        'Accept-Encoding' => 'gzip, deflate, br',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
        'Host' => 'tieba.baidu.com',
        'Referer' => 'https://tieba.baidu.com/f?kw=阿贝云',
        'X-Requested-With' => 'XMLHttpRequest'
    ];

    /**
     *
     */
    public $spider;

    /**
     * PostServer constructor.
     */
    public function __construct()
    {
        $this->spider = new Spider();
    }

    public function getBaiDuCookie()
    {
        $cookiejar = file_get_contents(COOKIE_PATH. 'tieba.tmp');
        $cookiejar = \json_decode($cookiejar, true);
        $client = self::getGuzzleClient($cookiejar, 'tieba.baidu.com');

        $params = [
            'ie' => 'utf-8',
            'kw' => '阿贝云',
            'fid' => '26729564',
            'tid' => '0',
            'vcode_md5' => '',
            'floor_num' => '0',
            'rich_text' => '1',
            'tbs' => '85f2fef167e547be1585543564',
            'content' => '今天我发现了一款免费的服务其软件啊, 呦呦呦 , 切克闹',
            'basilisk' => '1',
            'title' => '特大好消息, 特大好消息!!!',
            'prefix' => '',
            // 'mouse_pwd' => '13,9,15,19,6,11,8,8,54,14,19,15,19,14,19,15,19,14,19,15,19,14,19,15,19,14,19,15,54,8,14,6,8,54,14,12,9,9,19,8,9,7,15855437030631',
            // 'mouse_pwd_t' => time()*1000,
            'mouse_pwd_isclick' => '1',
            'nick_name' => '',
            '__type__' => 'thread',
            'geetest_success' => '0',
            '_BSK' => 'QxcFA0RfRkVEQgAZFltQRwsVTEdAUR8XWwEaD0ZGFBADHRREVBcOF1VTCRkaXgcWCRcOABQNVR5QXEoJAhtXBw0bUVcdDQwZAw0fDQIYAAZEHkQLVBMMF1cFBQBSUAAEFBdYABEPFAUOAEoBVVRKBwAGSQYHBk5RCQEaGRdEARcMFm9cCAFUR0oTWwVHDxYfUVYJGQoDAx0fHQUGABlVBlBMShkFBV0ZAgBaTB0dCwwMGAUCDx0UHVAFVUlQBwQeSR0CAFBJBwwJHBkcBwUPGA4FUhtKTV4JDxtQAAMeTk0CAA8ZAAAEHBocDwxfHlNQVRgUG0dHBRVYR1dAVlZBXVxbFkZZWwJdC01PEU0XPltVQwsTVBVbWlFRbhVLFhQXCANEX0YDBgZSBQEGU0kTWAsXDxRHR0NRFBcDA0RfRgMGBlIFAQZTSRNUChcPFAQDDhgaQlIQXEcWUERSC0EYWBIAX1BKGUFbQxlaUVZSElpKAxRQW1IWGVdbDRZUURRZWldSQV9bVhkVVwoDSkZfWQFaQxsGClJAVVBbQB9bV1ldGQVHFREJXHNbAFhRWRYWHV1RRkFbQUwaWFdWB0YPCghTV0VJWFFZFwdQRxRFUEZAWlhVVFcHQEoWBUNZWwlXVUURSUJBWUFAR1FURBhMWgleBAQUHUVDBEFBRE4DQ1RVUHBYVlhTWkwXShASV0QLFgZQDQECVlYGBgwZF0QAFwwUXlQKQQNJREYHFV8XemIuKRMZGkYHFgkVBwcOA0oQEVZECxZRBFlHUk5HXwYaDxUGAwQBBA0EVx5EEVcTDBUDQFpUFgxeWxhBWmdHR19aXx1PEh1FPV9XQwxDURcBClVQZRVIFh8XQQYaD0R8MykqExoVBAQWDUJUAgULGRdHABcMFExHE1dKRwcFFA1FU1VbEQAdF1MEFw4TU1dYS1BKEAJURAsUeTB5eBVOR0EEGg8XEQR3EwYKQQRBQ1dUFAV2QAcGD1cDA1NdUwQCBFADAA9XAwNTXVMEAgRQAwASUFcUAnwXGRZSBhQOGFMHXhUAShNaBkcPFk0KSHJ7GhkXXQIXDBRMRxNXSkcTABQNR3hbTQsJXVQXABsEEx1hXVZRCUUVRShlFgZVGwQMQjJYWw4BDhRLAwIdGHQWQgoAMVRUfAxBGwJRUh8GDhUdf3the3gUFQpbDQBGdlNUDlodFyENQ1pVUBoMAxsGGgsMXgVIVFIIFmQEU1VFC0oEBg8bBgITcFJTFw1WHFZLVQcHGVMMFhtACAAXAhdXVUBcWl1LXjlTKhNWW1EVGA==',
        ];
        $result = $client->request('post', $this->postTieBaApi, ['header' => $this->baiduHeaders, 'form_params' => $params]);
        p($result->getBody()->getContents());
    }


    public function serverReNew()
    {
        $params = [
            'cmd' => 'free_delay_add',
            'ptype' => 'vps',
            'url' => '',
            'yanqi_img' => '',
        ];

        $client = self::getGuzzleClient($this->getServerCookie(), 'abeiyun.com');
        $result = $client->request('post', $this->serverReNewApi, [
            'header' => $this->headers,
            'form_params' => $params
        ]);
        $rs = $result->getBody()->getContents();
        $rs = substr($rs, 3);
        p(json_decode($rs, true));
    }

    public function getServerCookie()
    {
        if (file_exists(COOKIE_PATH. 'server.tmp')) {
            $cookies = file_get_contents(COOKIE_PATH. 'server.tmp');
            $cookies = GuzzleHttp\json_decode($cookies, 1);
        } else {
            $client = self::getGuzzleClient();
            $loginParams = [
                'cmd' => 'login',
                'id_mobile' => 18533137035,
                'password' => 'LlF213344'
            ];
            $result = $client->request('post', $this->serverLoginApi, [
                'headers' => $this->headers,
                'form_params' => $loginParams
            ])->getHeaders();
            $serverSetCookie = $result['Set-Cookie'][0];
            preg_match('/\d+/', $serverSetCookie, $e);
            $cookies = [
                'session_id' => $e[0],
                'laiyunUrl' => 'https://www.abeiyun.com/control/',
                'Hm_lvt_491579dd37a8aefdc599ec4d556fd33f' => strtotime('-3 hours'), // 上次登录时间
                'Hm_lpvt_491579dd37a8aefdc599ec4d556fd33f' => time() // 当前时间戳
            ];
            file_put_contents(COOKIE_PATH. 'server.tmp', GuzzleHttp\json_encode($cookies));
        }
        return $cookies;
    }

    public function getServerInfo()
    {
        $params = [
            'cmd' => 'vps_list',
            'vps_type' => 'free'
        ];

        $client = self::getGuzzleClient($this->getServerCookie(), 'abeiyun.com');
        $result = $client->request('post', $this->serverCheckEndTimeApi, [
            'header' => $this->headers,
            'form_params' => $params
        ]);
        $rs = $result->getBody()->getContents();
        $rs = substr($rs, 3);
        $info = \GuzzleHttp\json_decode($rs, true);
        return $info['msg']['content'][0];
    }


    public static function getGuzzleClient($cookie = [], $domain = '')
    {

        $cookieJar = null;
        if (!empty($cookie)) {
            $cookieJar = CookieJar::fromArray($cookie, $domain);
        }
        if ($cookieJar) {
            return new Client(['cookies' => $cookieJar]);
        }
        return new Client();
    }

    public static function cookie2Array($cookiePath = '')
    {
        $data = [];
        if ($cookiePath) {
            $temp = file_get_contents($cookiePath);
            $temp = explode("\n", $temp);
            foreach ($temp as $row) {
                $rowArray = explode(': ', $row);
                $data[trim($rowArray[0])] = trim($rowArray[1]);
            }
        }
        return $data;
    }

}


$cookieArray = PostServer::cookie2Array(COOKIE_PATH. 'hospital.cookie');
$client = PostServer::getGuzzleClient($cookieArray, 'mp.mhealth100.com');

$url = "https://h5api.m.taobao.com/h5/mtop.tmall.kangaroo.core.service.route.aldlampservicefixedres/1.0/?jsv=2.4.0&appKey=12574478&t=1598274744740&sign=3f852cde2db23777710ada8251ca18c4&api=mtop.tmall.kangaroo.core.service.route.AldLampServiceFixedRes&dataType=jsonp&v=1.0&timeout=3000&preventFallback=true&type=jsonp&callback=mtopjsonp1&data=%7B%22curPageUrl%22%3A%22https%253A%252F%252Fmarket.m.taobao.com%252Fapps%252Fyouhaohuo%252Findex%252Ftag.html%253Fwh_weex%253Dtrue%2526type%253Dsearch%2526key%253D%2525E7%25258E%2525A9%2525E5%252585%2525B7%22%2C%22appId%22%3A%226696424%22%2C%22tce_sid%22%3A%221891397%22%2C%22tce_vid%22%3A%220%22%2C%22topic%22%3A%22search%22%2C%22src%22%3A%22phone%22%2C%22params%22%3A%22%7B%5C%22resId%5C%22%3A%5C%226696424%5C%22%2C%5C%22bizId%5C%22%3A%5C%222020%5C%22%2C%5C%22tce_sid%5C%22%3A%5C%221891397%5C%22%2C%5C%22tce_vid%5C%22%3A%5C%220%5C%22%2C%5C%22topic%5C%22%3A%5C%22search%5C%22%2C%5C%22env%5C%22%3A%5C%22dev%5C%22%2C%5C%22pageNo%5C%22%3A1%2C%5C%22psId%5C%22%3A%5C%2251817%5C%22%2C%5C%22bizCode%5C%22%3A%5C%22steins.goodItem%5C%22%2C%5C%22type%5C%22%3A%5C%22search%5C%22%2C%5C%22page%5C%22%3A1%2C%5C%22pageSize%5C%22%3A20%2C%5C%22src%5C%22%3A%5C%22phone%5C%22%2C%5C%22source%5C%22%3A%5C%22tceFaas%5C%22%2C%5C%22key%5C%22%3A%5C%22%E7%8E%A9%E5%85%B7%5C%22%7D%22%2C%22_pvuuid%22%3A1598274744719%2C%22isbackup%22%3Atrue%7D";
$response = $client->get($url, []);
p($response);

$rs = $response->getBody()->getContents();
$rs = json_decode($rs, 1);
















































$jar = CookieJar::fromArray([
    'session_id' => '1585313118121533203',
    'laiyunUrl' => 'https://www.abeiyun.com/control/',
    'Hm_lvt_491579dd37a8aefdc599ec4d556fd33f' => '1585094347,1585313101',
    'Hm_lpvt_491579dd37a8aefdc599ec4d556fd33f' => '1585313101'
], 'abeiyun.com');

$client = new \GuzzleHttp\Client(['cookies' => $jar]);
















function getOpenID($len = 10)
{
    $str = 'abcdefghigjklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ0123456789';
    $randStr = '';
    for ($i = 0, $cnt = $len; $i < $cnt; $i++) {
        $randStr .= $str{mt_rand(0, (strlen($str)-1))};
    }
    return str_shuffle($randStr);
}

