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
set_time_limit(0); // script keep running;

$spider = new Spider();

$html = $spider->setUrl($url)
    ->setHeader([
        ':authority' => 'wannianrili.51240.com',
        ':method' => 'GET',
        ':path' => '/',
        ':scheme' => 'https',
        'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
        'accept-language' => 'zh-CN,zh;q=0.9',
        'cache-control' => 'no-cache',
        'cookie' => 'Hm_lvt_fbe0e02a7ffde424814bef2f6c9d36eb=1559730643; Hm_lpvt_fbe0e02a7ffde424814bef2f6c9d36eb=1559730841',
        'pragma' => 'no-cache',
        'upgrade-insecure-requests' => '1',
        'user-agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36'
    ])
    ->get();

exit($html);




