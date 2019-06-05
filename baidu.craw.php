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
$url = 'http://private70.ghuws.win/thread0806.php?fid=22';
$crawRootPath = './crawFiles/yinduo/';

set_time_limit(0); // script keep running;

$spider = new Spider();
$rs = $spider->setUrl($url)
             ->setHeader([
                 'Host' => 'private70.ghuws.win',
                 'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                 'Upgrade-Insecure-Requests' => '1',
                 #'accept-encoding' => 'gzip, deflate, br', // 发送编码之后的数据
                 'accept-language' => 'zh-CN,zh;q=0.9',
                 'cache-control' => 'no-cache',
                 'pragma' => 'no-cache',
                 'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
                 'upgrade-insecure-requests' => '1',
                 'referer' => 'https://hs.etet.men/index.php',
                 'user-agent' => 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Mobile Safari/537.36',
                 'Cookie' => 'ismob=1; hiddenface=; cssNight=; __cfduid=d9195a93c4a594e3459abb8c62987d9a21558925417; PHPSESSID=sb9ls5v5l3ou823hc24t8m9aj1; UM_distinctid=16aff16dd8511c-0a45c2d4e2a94b-52504913-49a10-16aff16dd8b1d6; 227c9_lastvisit=0%091559626523%09%2Fread.php%3Ftid%3D3542890; CNZZDATA950900=cnzz_eid%3D254784805-1559056130-%26ntime%3D1559628669'
             ])
             ->post();


p($rs);





