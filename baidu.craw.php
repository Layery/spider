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



#$html = $spider->setUrl($url)->get();

foreach ([1, 2, 3, 4, 5] as $val ) 
{
    switch ($val) {
        case 1:
            echo '1111'. "\n";
            break;
        

        case 3:
            echo 'sdf';
            continue;
            echo 'ccc';

            break;
        default:
            # code...
            break;
    }
    echo $val. '----'. "\n";
} 




