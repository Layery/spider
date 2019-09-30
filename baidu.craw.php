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
$url = 'http://wx567336e2a104442f.wxa.dzsaascdn.com/creeper/Vote';
$url = 'https://www.ffvan.com/video/show/id/68845';
$url = 'http://www.baidu.com';
set_time_limit(0); // script keep running;

$spider = new Spider();


function getOpenID($len = 10)
{
    $str = 'abcdefghigjklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ0123456789';
    $randStr = '';
    for ($i = 0, $cnt = $len; $i < $cnt; $i++) {
        $randStr .= $str{mt_rand(0, (strlen($str)-1))};
    }
    return str_shuffle($randStr);
}

$html = $spider->setUrl($url)
    ->setHeader()
    ->get(['tt' => 'debug']);

p($html);



