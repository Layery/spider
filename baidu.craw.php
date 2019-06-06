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
$url = 'https://kkembed.kdwshell.com/get_file/3/a7b22583cac2638c053858d31ea5346a/61000/61532/61532.mp4';
$url = 'https://wannianrili.51240.com/ajax/?q=2019-08&v=19021213';
$crawRootPath = './crawFiles/yinduo/';

set_time_limit(0); // script keep running;

$spider = new Spider();
$rs = $spider->setUrl($url)
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
             ->post();

exit($rs);





