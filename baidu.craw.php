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

$url = 'https://www.yinduowang.com/index/ajaxsignin';
$url = 'https://www.yinduowang.com/public/login';
$url = 'https://kkembed.kdwshell.com/get_file/3/a7b22583cac2638c053858d31ea5346a/61000/61532/61532.mp4';
$crawRootPath = './crawFiles/yinduo/';




set_time_limit(0); // script keep running;

$spider = new Spider();
$rs = $spider
        ->setUrl($url)
        ->post();

p($rs);
