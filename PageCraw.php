<?php
/**
 * Created by PhpStorm.
 * User: llf
 * Date: 2018/12/25
 * Time: 16:13
 */
require './vendor/autoload.php';
require './vendor/lib/Spider.php';
include 'E:/www/global';

use Symfony\Component\DomCrawler\Crawler;

$crawParams = isset($argv[1]) ? $argv[1] : 'web';
$search = 'https://yandex.ru/search/?text=%E8%8D%89%E6%A6%B4%E7%A4%BE%E5%8C%BA';
$baseUrl = '';
if ($crawParams == 'web') {
    $baseUrl = 'https://hh.flexui.win/thread0806.php?fid=22';
    $baseUrl = 'https://cl.wpio.xyz/thread0806.php?fid=22';
} else if ($crawParams == 'img') {
    $baseUrl = 'https://hh.flexui.win/thread0806.php?fid=8';
    $baseUrl = 'https://hh.flexui.win/thread0806.php?fid=16';
    $baseUrl = 'https://hs.dety.men/thread0806.php?fid=16';
    $baseUrl = 'https://cl.wpio.xyz/thread0806.php?fid=8';
}
//http://cl.wpio.xyz/htm_mob/22/1903/3469154.html
$hostInfo = pathinfo($baseUrl);
$crawRootPath = './crawFiles/';
$crawPagePath = $crawRootPath . 'crawWeb/';
$crawImgPath = $crawRootPath . 'crawImg/';
$saveDictPath = $crawRootPath . 'dict/web/';
$saveImgPath = $crawRootPath . 'dict/img/';
$tmplPath = './vendor/tmpl/';

$runTime = time();
$tplFile = file_get_contents($tmplPath . 'view.tpl');
$spider = new Spider();
$spider->setUnCheckSsl()
    ->setReturnCharset()
    ->setReturnStream()
    ->setHeader([
        'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
        #'accept-encoding' => 'gzip, deflate, br', // 发送编码之后的数据
        'accept-language' => 'zh-CN,zh;q=0.9',
        'cache-control' => 'no-cache',
        'pragma' => 'no-cache',
        'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
        'upgrade-insecure-requests' => '1',
        'user-agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:63.0) Gecko/20100101 Firefox/63.0',
//        'user-agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 YaBrowser/18.10.2.163 Yowser/2.5 Safari/537.36'
    ]);


if ($crawParams == 'img') {
    for ($i = 1, $cnt = 1; $i <= $cnt; $i ++) {
        $currDictFile = $saveImgPath . $i;
        if (is_file($currDictFile)) {
            $currDictFileCreateTime = filectime($currDictFile);
            if ($runTime-$currDictFileCreateTime < 86400) { // continue dict files which createTime less then one day
               logWrite("the ". $i . " page has parsed");
               continue;
            }
        }
        $url = $baseUrl . '&page='. $i;
        $htmlPath = '';
        if (!is_file($crawImgPath . $i . '/F') || $runTime-filectime($crawImgPath . $i . '/F') > 86400) { // reCraw sourceFiles which catchTime more then one day
            logWrite('begin catching the '. $i . ' page');
            $html = $spider->post($url);
            $html = mb_convert_encoding($html, 'utf-8', 'gbk');
            preg_match("/<body>(.*?)<\/body>/s", $html, $m);
            if (!is_dir($crawImgPath . $i)) {
                mkdir($crawImgPath. $i, 0777, true);
            }
            file_put_contents($crawImgPath . $i . '/F', $m[0]);
        }
        $htmlPath = $crawImgPath . $i . '/F';
        $htmlStr = file_get_contents($htmlPath);
        $data = [];
        $crawler = new Crawler($htmlStr);
        try {
            logWrite('begin parse the '. $i . ' page');
            $contentTable = $crawler->filterXPath('//div[@class="t"][2]/table');
            $tdDom = $contentTable->filterXPath('//tr[contains(@class,"tr3 t_one tac")]/td')->each(function (Crawler $node, $i) use ($baseUrl, &$data) {
                if ($node->attr('class')) {
                    $title = preg_replace('/\s/', '', $node->text());
                    if (strpos($title, '↑') !== false) {
                        return;
                    }
                    $aDom = $node->filterXPath('//h3/a');
                    $href = pathinfo($baseUrl);
                    $href = $href['dirname'] . '/'. $aDom->attr('href');
                    $data[] = [
                        'href' => $href,
                        'title' => $title
                    ];
                }
            });
            $totalItem = count($data);
            logWrite('parse the '. $i . ' page success count '. $totalItem. ' item');
            // then craw next page
            if (!empty($data)) {
                $htmlPath = $htmlStr = '';
                $crawler = null;
                $dictStr = '';
                foreach ($data as $key => $row) {
                    $itemIndex = $key + 1;
                    #$itemIndex = 76; // debug
                    logWrite('begin catching the '. $itemIndex. ' item '. ' title: '. $row['title']);
                    $html = $spider->post($row['href']);
                    preg_match_all("/<input.*?type=[\'|\"]image[\'|\"]>/", $html, $m);
//                    preg_match_all("/<input.*?type=\"image\">/", $html, $m);
                    $imageList = $m[0];
                    preg_match('/\].*?\]/',$row['title'], $rs);
                    $title = substr($rs[0], 1);
                    $imagePath = mb_convert_encoding($saveImgPath. $title. '-'. $itemIndex, 'gbk') . "/";
                    if (!is_dir($imagePath)) {
                        mkdir($imagePath, 0777, true);
                    }
                    foreach ($imageList as $imgIndex => $image) {
                        $imgIndex = $imgIndex + 1;
                        $image = explode('data-src=', $image);
                        $image = preg_match('/http.*?\.(gif|jpg|png|jpeg)/', $image[1], $e);
                        $ext = $e[1];
                        $res = $spider->download($e[0]);
                        file_put_contents($imagePath . $imgIndex . '.'. $ext, $res);
                        logWrite('save '. $imgIndex . 'th'. ' success');
                    }
                }
            }
        } catch (\Exception $e) {
            logWrite('catch Exception on the '. $i . ' page '. $itemIndex . ' item');
        }
        logWrite("sleep three seconds please wait...");
        sleep(3);
    }
}


if ($crawParams == 'web') {
    for ($i = 1, $cnt = 30; $i <= $cnt; $i ++) {
        $currDictFile = $saveDictPath . $i. '.html';
        if (is_file($currDictFile)) {
            $currDictFileCreateTime = filectime($currDictFile);
            if ($runTime-$currDictFileCreateTime < 86400) { // continue dict files which createTime less then one day
                logWrite("the ". $i . " page has parsed");
                continue;
            }
        }
        $url = $baseUrl . '&page='. $i;
        $htmlPath = '';
        if (!is_file($crawPagePath . $i . '/F') || $runTime-filemtime($crawPagePath . $i . '/F') > 86400) { // reCraw sourceFiles which catchTime more then one day
            logWrite('begin catching the '. $i . ' page');
            $html = $spider->setReturnCharset()->post($url);
            preg_match("/<body>(.*?)<\/body>/s", $html, $m);
            if (!is_dir($crawPagePath . $i)) {
                mkdir($crawPagePath. $i, 0777, true);
            }
            file_put_contents($crawPagePath . $i . '/F', $m[0]);
        }
        $htmlPath = $crawPagePath . $i . '/F';
        $htmlStr = file_get_contents($htmlPath);
        $data = [];
        $crawler = new Crawler($htmlStr);
        try {
            logWrite('begin parse the '. $i . ' page');
            $contentTable = $crawler->filterXPath('//div[@class="t"][2]/table');
            $tdDom = $contentTable->filterXPath('//tr[contains(@class,"tr3 t_one tac")]/td')->each(function (Crawler $node, $i) use ($hostInfo, &$data) {
                if ($node->attr('class')) {
                    $title = preg_replace('/\s/', '', $node->text());
                    $aDom = $node->filterXPath('//a');
                    if (strpos($title, '↑') !== false) {
                        return;
                    }
                    $href = $hostInfo['dirname'] . '/'. $aDom->attr('href');
                    $data[] = [
                        'href' => $href,
                        'title' => $title
                    ];
                }
            });
            $totalItem = count($data);
            logWrite('parse the '. $i . ' page success count '. $totalItem. ' item');
            // then craw next page
            if (!empty($data)) {
                $htmlPath = $htmlStr = '';
                $crawler = null;
                $dictStr = '';
                foreach ($data as $key => $row) {
                    $itemIndex = $key + 1;
                    if ($itemIndex == 1) $row['href'] = 'https://cl.wpio.xyz/htm_data/22/1903/3469161.html';
                    #$itemIndex = 35; // debug
                    if (!is_file($crawPagePath . $i . '/S'. $itemIndex) || $runTime-filemtime($crawPagePath . $i . '/S'. $itemIndex) > 86400) {
                        $html = $spider->post($row['href']);
                        // div@class='tpc_content do_not_catch'
                        preg_match_all("/<h4.*?>.*?<\/h4>|<div class=\"tpc_content do_not_catch\">.*?<\/div>/s", $html, $content);
                        $content = $content[0];
                        file_put_contents($crawPagePath . $i .'/S'. $itemIndex, $content[0].$content[1]);
                    }
                    $htmlPath = $crawPagePath . $i . '/S'. $itemIndex;
                    $htmlStr = file_get_contents($htmlPath);
                    $crawler = new Crawler($htmlStr);
                    $sourceTitle = $crawler->filterXPath('//h4')->text();
                    $sourceLink = 'src=http://www.baidu.com';
                    $aCount = $crawler->filterXPath('//div[contains(@class,"tpc_content")]/a[2]')->count();
                    if ($aCount) {
                        $sourceLink = $crawler->filterXPath('//div[contains(@class,"tpc_content")]/a[2]')->attr('onclick');
                        $sourceLink = explode('src=', $sourceLink);
                        $sourceLink = trim($sourceLink[1], '\'');
                    } else {
                        $sourceLink = $crawler->filterXPath('//div[contains(@class,"tpc_content")]')->text();
                        $sourceLink = preg_match("~http[s]?://([\w-]+\.)+[\w-]+(/[\w- ./?%&=]*)~", $sourceLink, $m);
                        $sourceLink = $sourceLink[0];
                    }
                    $dictStr .= '<tr>';
                    $dictStr .= '<td class="td-id"><span class="num">'. $itemIndex. '</span></td>';
                    $dictStr .= '<td class="td-title"><a href="'. $sourceLink. '" target="_bank">'.$sourceTitle.'</a></td>';
                    $dictStr .= '</tr>';
                }
                $dictStr = preg_replace('/\<\{CONTENT\}\>/s', $dictStr, $tplFile);
                if (!is_dir($saveDictPath)) {
                    mkdir($saveDictPath, 0777, true);
                }
                file_put_contents($saveDictPath. $i. '.html', $dictStr, FILE_APPEND);
                logWrite('save the '. $i . ' dict success');
            }
        } catch (\Exception $e) {
            logWrite('NOTICE: catch Exception on the '. $i . ' page '. $itemIndex . ' item');
            continue;
        }
        logWrite("sleep three seconds please wait...");
        sleep(3);
    }
}





















