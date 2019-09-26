<?php
/**
 * Created by PhpStorm.
 * User: llf
 * Date: 2018/12/29
 * Time: 10:05
 */
require './vendor/autoload.php';
require './vendor/lib/Spider.php';
include 'E:/www/global';

use Symfony\Component\DomCrawler\Crawler;

$crawRootPath = './crawFiles/yinduo/';
$url = 'https://dw27.malavida.com/dwn/8573ff0bfe7d80aa1153d941614cc62f4ba0bf52cb50f1e31e432ed247b9941d/com.nintendo.zara_3.0.14.apk';
$url = 'http://packagess.deepin.com:8088/releases/15.10.1/deepin-15.10.1-amd64.iso';
$url = 'https://ffvan.com/video/index/cid/10';
$url = 'https://ffvan.com/video/show/id/67389';
$url = 'https://www.ffvan.com/video/show/id/68845';
$url = 'http://wx567336e2a104442f.wxa.dzsaascdn.com/creeper/Vote';
set_time_limit(0); // script keep running;

$spider = new Spider();

$str = "http://wx567336e2a104442f.wxa.dzsaascdn.com/creeper/index?aid=wx567336e2a104442f&cpid=00d9ae25a1004b4da3de2256bfaf06d4&optionid=46f4727b62bd4c849cff34266be28388&appid=wx567336e2a104442f";

$params = [
    'cpid' => '00d9ae25a1004b4da3de2256bfaf06d4',
    'optionid' => '46f4727b62bd4c849cff34266be28388',
    'openid' => 'oRgIb6AAcH2cBCLjbfZf9KwyZaKM',
    'latitude' => '',
    'longitude' => ''
];



function getOpenID()
{
    $str = 'abcdefghigjklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ0123456789';
    $randStr = '';
    for ($i = 0, $cnt = 10; $i < $cnt; $i++) {
        $randStr .= $str{mt_rand(0, 64)};
    }
    return $randStr;
}

$spider->setUrl($url)
    ->setHeader([
        'Host' => 'wx567336e2a104442f.wxa.dzsaascdn.com',
        'Connection' => 'keep-alive',
        'Content-Length' => '115',
        'Accept' => '*/*',
        'Origin' => 'http://wx567336e2a104442f.wxa.dzsaascdn.com',
        'X-Requested-With' => 'XMLHttpRequest',
        'User-Agent' => 'Mozilla/5.0 (Linux; Android 7.0; SM-G9200 Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/66.0.3359.126 MQQBrowser/6.2 TBS/044904 Mobile Safari/537.36 MicroMessenger/6.7.3.1360(0x26070333) NetType/4G Language/zh_CN Process/tools',
        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
        'Referer' => 'http://wx567336e2a104442f.wxa.dzsaascdn.com/creeper/index?aid=wx567336e2a104442f&cpid=00d9ae25a1004b4da3de2256bfaf06d4&optionid=46f4727b62bd4c849cff34266be28388',
        'Accept-Encoding' => 'gzip, deflate',
        'Accept-Language' => 'zh-CN,zh-CN;q=0.9,en-US;q=0.8',
//        'Cookie' => 'ASP.NET_SessionId=h2cdtcklz0dnav5ifeek4uug; Hm_lvt_917473e977996fda4a9af93570a7464c=1568802621; Hm_lpvt_917473e977996fda4a9af93570a7464c=1568811146',
    ]);
while (true) {
    $params['openid'] = getOpenID();


    sleep(mt_rand(1, 5));
}






