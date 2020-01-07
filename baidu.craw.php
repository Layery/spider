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


p(getOpenID(5));

$html = $spider->setUrl($url)->get();




