<?php
/**
 * Created by PhpStorm.
 * User: llf
 * Date: 2018/12/25
 * Time: 16:13
 */
ini_set('max_execution_time', 0); // 不限制执行时间
ini_set('memory_limit','4096M');
set_time_limit(0); // 不限制超时时间


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
    $baseUrl = 'https://cl.wpio.xyz/thread0806.php?fid=16';
    $baseUrl = 'https://cl.wpio.xyz/thread0806.php?fid=7'; // 技术讨论
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
    for ($i = 1, $cnt = 1; $i <= $cnt; $i++) {
        $url = $baseUrl . '&page=' . $i;
        logWrite('begin catching the ' . $i . ' page');
        $html = $spider->setUrl($url)
//                ->setReturnCharset()
            ->get();
        $data = [];
        $crawler = new Crawler($html);
        $postTitle = $crawler->filterXPath('//title')->text();
        try {
            logWrite('begin parse the ' . $i . ' page');
            $contentTable = $crawler->filterXPath('//div[@class="t"][2]/table');
            $tdDom = $contentTable->filterXPath('//tr[contains(@class,"tr3 t_one tac")]/td')->each(function (Crawler $node, $i) use ($baseUrl, &$data) {
                if ($node->attr('class')) {
                    $title = preg_replace('/\s/', '', $node->text());
                    if (strpos($title, '↑') !== false) {
                        return;
                    }
                    $aDom = $node->filterXPath('//h3/a');
                    $href = pathinfo($baseUrl);
                    $href = $href['dirname'] . '/' . $aDom->attr('href');
                    $data[] = [
                        'href' => $href,
                        'title' => $title
                    ];
                }
            });
            $totalItem = count($data);
            logWrite('parse the ' . $i . ' page success count ' . $totalItem . ' item');
            // then craw next page
            if (!empty($data)) {
                $htmlPath = $htmlStr = '';
                $crawler = null;
                $dictStr = '';
                foreach ($data as $key => $row) {
                    $itemIndex = $key + 1;
                    #$itemIndex = 76; // debug
                    $imageList = [];
                    switch ($postTitle) {
                        case strpos($postTitle, '技術討論區') !== false:
                            #exit(mb_convert_encoding($html, 'utf-8'));
                            if (!preg_match('/［\d+[Pp]］/', $row['title']) || !preg_match('/\[\d+[Pp]\]/is', $row['title'])) {
                                logWrite('continue item ' . $i . ' page title: ' . $row['title']);
                                continue;
                            }
                            p($row);
                            logWrite('begin catching the ' . $itemIndex . ' item ' . ' title: ' . $row['title']);
                            $html = $spider->setUrl($row['href'])->get();
                            echo "aaa\n";
                            p($html);
                            preg_match_all("//is", $html, $m);
                            $imageList = $m[0];
                            break;
                        default:
                            logWrite('begin catching the ' . $itemIndex . ' item ' . ' title: ' . $row['title']);
                            $html = $spider->setUrl($row['href'])->get();
                            @preg_match_all("/<input.*?type=[\'|\"]image[\'|\"]>/", $html, $m);
//                            preg_match_all("/<input.*?type=\"image\">/", $html, $m);
                            $imageList = $m[0];
                            break;
                    }
                    $imagePath = mb_convert_encoding($saveImgPath . $row['title'], 'gbk') . "/";
                    if (!is_dir($imagePath)) {
                        mkdir($imagePath, 0777, true);
                    }
                    foreach ($imageList as $imgIndex => $image) {
                        $imgIndex = $imgIndex + 1;
                        $image = explode('data-src=', $image);
                        $image = @preg_match('/http.*?\.(gif|jpg|png|jpeg)/', $image[1], $e);
                        $ext = $e[1];
                        $res = @file_get_contents($e[0]);
                        file_put_contents($imagePath . $imgIndex . '.' . $ext, $res);
                        logWrite('save ' . $imgIndex . 'th' . ' success');
                    }
                }
            }
        } catch (\Exception $e) {
            logWrite('catch Exception on the ' . $i . ' page ' . $itemIndex . ' item');
        }
    }
}


if ($crawParams == 'web') {
    $data = [];
    $searchList = [];
    for ($i = $argv[2] ? $argv[2] : 1, $cnt = $argv[3] ? $argv[3] : 2; $i <= $cnt; $i++) {
        $currDictFile = $saveDictPath . $i . '.html';
        if (is_file($currDictFile)) {
            $currDictFileCreateTime = filectime($currDictFile);
            if ($runTime - $currDictFileCreateTime < 86400) { // continue dict files which createTime less then one day
                logWrite("the " . $i . " page has parsed");
                continue;
            }
        }
        $url = $baseUrl . '&page=' . $i;
        logWrite('begin catching the ' . $i . ' page');
        $html = $spider->setUrl($url)
            ->setReturnCharset()
            ->post();
        preg_match("/<body>(.*?)<\/body>/s", $html, $m);
        $htmlStr = $m[0];
        $crawler = new Crawler($htmlStr);
        try {
            logWrite('begin parse the ' . $i . ' page');
            $contentTable = $crawler->filterXPath('//div[@class="t"][2]/table');
            $tdDom = $contentTable->filterXPath('//tr[contains(@class,"tr3 t_one tac")]/td')->each(function (Crawler $node) use (&$data, $baseUrl, $hostInfo) {
                if ($node->attr('class')) {
                    $title = preg_replace('/\s/', '', $node->text());
                    $aDom = $node->filterXPath('//a');
                    if (strpos($title, '↑') !== false) {
                        return;
                    }
//                    if (!preg_match("/SM|sm|调教|变态|屎|另类/is", $title)) {
                    if (!preg_match("/阿姨/is", $title)) {
                        return;
                    }
                    $href = $hostInfo['dirname'] . '/' . $aDom->attr('href');
                    $data[] = [
                        'href' => $href,
                        'title' => $title
                    ];
                }
            });
            $searchList[] = $data;
            $totalItem = count($data);
            logWrite('parse the ' . $i . ' page success count ' . $totalItem . ' item');
            // then craw next page
        } catch (\Exception $e) {
            p($e->getTraceAsString());
            logWrite('NOTICE: catch Exception on the ' . $i . ' page ' . $itemIndex . ' item');
            continue;
        }

        logWrite("sleep three seconds please wait...");
        sleep(1);
    }
    if (!empty($searchList)) {
        $htmlPath = $htmlStr = '';
        $crawler = null;
        $dictStr = '';

        foreach (end($searchList) as $key => $row) {
            $itemIndex = $key + 1;
            #$itemIndex = 35; // debug
            $html = $spider->setUrl($row['href'])
                ->setReturnCharset()
                ->get();
            // div@class='tpc_content do_not_catch'
            preg_match_all("/<h4.*?>.*?<\/h4>|<div class=\"tpc_content do_not_catch\">.*?<\/div>/s", $html, $content);
            $htmlStr = implode('', $content[0]);
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
            $dictStr .= '<td class="td-id"><span class="num">' . $itemIndex . '</span></td>';
            $dictStr .= '<td class="td-title"><a href="' . $sourceLink . '" target="_bank">' . $sourceTitle . '</a></td>';
            $dictStr .= '</tr>';
        }
        $dictStr = preg_replace('/\<\{CONTENT\}\>/s', $dictStr, $tplFile);
        if (!is_dir($saveDictPath)) {
            mkdir($saveDictPath, 0777, true);
        }
        file_put_contents($saveDictPath . 'search' . '.html', $dictStr, FILE_APPEND);
        logWrite('save the search dict success');
    }
    die;
    if (!empty($data)) {
        $htmlPath = $htmlStr = '';
        $crawler = null;
        $dictStr = '';
        foreach ($data as $key => $row) {
            $itemIndex = $key + 1;
            #$itemIndex = 35; // debug
            $html = $spider->setUrl($row['href'])
                ->setReturnCharset()
                ->get();
            // div@class='tpc_content do_not_catch'
            preg_match_all("/<h4.*?>.*?<\/h4>|<div class=\"tpc_content do_not_catch\">.*?<\/div>/s", $html, $content);
            $htmlStr = implode('', $content[0]);
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
            $dictStr .= '<td class="td-id"><span class="num">' . $itemIndex . '</span></td>';
            $dictStr .= '<td class="td-title"><a href="' . $sourceLink . '" target="_bank">' . $sourceTitle . '</a></td>';
            $dictStr .= '</tr>';
        }
        $dictStr = preg_replace('/\<\{CONTENT\}\>/s', $dictStr, $tplFile);
        if (!is_dir($saveDictPath)) {
            mkdir($saveDictPath, 0777, true);
        }
        file_put_contents($saveDictPath . $i . '.html', $dictStr, FILE_APPEND);
        logWrite('save the ' . $i . ' dict success');
    }
}





















