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

$url = 'https://www.yinduowang.com/public/login';
$crawRootPath = './crawFiles/yinduo/';

set_time_limit(0); // script keep running;

$spider = new Spider();
$rs = $spider
        ->setUrl($url)
        ->setHeader([
            'Accept' => '*/*',
            'Accept-Language' => 'zh-CN,zh;q=0.9,und;q=0.8',
            'Connection' => 'keep-alive',
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Host' => 'www.yinduowang.com',
            'Origin' => 'https://www.yinduowang.com',
            'Referer' => 'https://www.yinduowang.com/login',
            'Upgrade-Insecure-Requests' => '1',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.67 Safari/537.36',
            'X-Requested-With' => 'XMLHttpRequest'
        ])
        ->setCookie('E:\www\spider\cookie', 'yinduo')
        ->setXsrfToken()
        ->setUnCheckSsl()
        ->post([
            'mobile' => '15512973505',
            'pwd' => 'LlF213344',
            'set_verify_input' => '',
        ]);

p(json_decode($rs));
