<?php
/**
 * Created by PhpStorm.
 * User: llf
 * Date: 2018/12/25
 * Time: 16:13
 */
ini_set('max_execution_time', 0); // 不限制执行时间
ini_set('memory_limit','512M');
error_reporting(E_ALL ^E_NOTICE);
set_time_limit(0); // 不限制超时时间


require './vendor/autoload.php';
require './vendor/lib/Spider.php';
include 'E:/www/global';

use Symfony\Component\DomCrawler\Crawler;


//    fwrite(STDOUT, iconv('utf-8', 'gbk', "请输入结束页: \n"));
//    $end = trim(fgets(STDIN));
//    fwrite(STDOUT, "Hello,$name"); //在终端回显输入

$crawParams = isset($argv[1]) ? $argv[1] : 'web';
$search = 'https://yandex.ru/search/?text=%E8%8D%89%E6%A6%B4%E7%A4%BE%E5%8C%BA';
$baseUrl = '';
if ($crawParams == 'web') {
    $baseUrl = 'https://hs.etet.men/thread0806.php?fid=22';
} else if ($crawParams == 'img') {
    $baseUrl = 'https://hs.etet.men/thread0806.php?fid=7'; // 技术讨论
    $baseUrl = 'https://hs.etet.men/thread0806.php?fid=16';
    $baseUrl = 'https://hs.etet.men/thread0806.php?fid=8';
}
//http://cl.wpio.xyz/htm_mob/22/1903/3469154.html
$hostInfo = pathinfo($baseUrl);
$crawRootPath = './crawFiles/';
$crawPagePath = $crawRootPath . 'crawWeb/';
$crawImgPath = $crawRootPath . 'crawImg/';
$saveDictPath = $crawRootPath . 'dict/web/';
$saveImgPath = $crawRootPath . 'dict/img/';
$loopStart = (isset($argv[2]) && $argv[2]) ? $argv[2] : 1;
$loopEnd = (isset($argv[3]) && $argv[3]) ? $argv[3] : 5;
$filter = (isset($argv[4]) && $argv[4]) ? $argv[4] : '';
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
    $data = [];
    for ($i = $loopStart; $i <= $loopEnd; $i++) {
        $url = $baseUrl . '&page=' . $i;
        logWrite('begin catching the ' . $i . ' page');
        $html = $spider->setUrl($url)
                ->get();
//        $html = file_get_contents('./debug.html');
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $crawler = new DOMXPath($dom);
        $postTitle = $crawler->query('//title')->item(0)->textContent;
        try {
            logWrite('begin parse the ' . $i . ' page');
            $tableDom = $crawler->query('//div[@class="t"][2]/table');
            if ($tableDom->length) {
                foreach ($tableDom as $table) {
                    $trDom = $crawler->query('//tr[contains(@class,"tr3 t_one tac")]', $table);
                    if ($trDom->length) {
                        foreach ($trDom as $tr) {
                            $tdDom = $crawler->query('./td', $tr);
                            if ($tdDom->length >= 5) {
                                foreach ($tdDom as $item => $td) {
                                    if ($td instanceof DOMElement && $td->attributes->getNamedItem('class')->nodeValue == 'tal') {
                                        $title = preg_replace('/\s/', '', $td->textContent);
//                                        $title = mb_convert_encoding($title, 'gbk');
                                        if (strpos($title, '↑') !== false) {
                                            continue;
                                        }
                                        if (!preg_match("/\(\d+[pP]\)|\[\d+[pP]\]|［\d+[pP]］/", $title)) { // @ todo 不存在[p]的干掉
                                            continue;
                                        }
                                        preg_match('/\[.*?\].*?\[\d+[pP]\]/is', $title, $m);
                                        $title = $m[0];
                                        $href = $crawler->query('.//a', $td)->item(0)->attributes->getNamedItem('href')->textContent;
                                        $href = $hostInfo['dirname'] . '/' . $href;
                                        $_SESSION['data'][] = [
                                            'href' => $href,
                                            'title' => $title
                                        ];
                                        #p(mb_convert_encoding($title, 'gbk'));
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $data = $_SESSION['data'];
            $totalItem = count($data);
            logWrite('parse the ' . $i . ' page success count ' . $totalItem . ' item');
            // then craw next page
            if (!empty($data)) {
                $htmlPath = $htmlStr = '';
                $crawler = null;
                foreach ($data as $key => $row) {
                    $imageList = [];
                    $imgTitle = str_replace([',', '，','?', '？', '.', '（', '）', ' ', '　'], '', $row['title']);
                    $itemIndex = $key + 1;
                    #$itemIndex = 76; // debug
                    $continueStatus = false;
                    $imagePath = mb_convert_encoding($saveImgPath . $imgTitle, 'gbk') . "/";
                    switch ($postTitle) {
                        case strpos($postTitle, '技術討論區') !== false:
                            if (!is_dir($imagePath)) {
                                @mkdir($imagePath, 0777, true);
                            } else {
                                logWrite($imgTitle. ' has download ready continue it');
                                continue;
                            }
                            logWrite('begin catching the ' . $itemIndex . ' item ' . ' title: ' . $imgTitle);
                            $html = $spider->setUrl($row['href'])->get();
                            preg_match_all('/\<img.*?data-src=.*?\>/is', $html, $m);
                            foreach ($m[0] as $href) {
                                $imageList[] = $href;
                            }
                            break;
                        default:
                            if (strpos($imgTitle, '歐') !== false) {
                                $continueStatus = true;
                                continue;
                            }
                            if (!is_dir($imagePath)) {
                                @mkdir($imagePath, 0777, true);
                            } else {
                                logWrite($imgTitle. ' has download ready continue it');
                                continue;
                            }
                            logWrite('begin catching the ' . $itemIndex . ' item ' . ' title: ' . $imgTitle);
                            $html = $spider->setUrl($row['href'])->get();
                            preg_match_all('/\<input.*?type=[\'\"]image[\'\"]\>/is', $html, $m);
                            $imageList = $m[0];
                            break;
                    }
                    if ($continueStatus) continue;

                    $countImage = count($imageList);
                    foreach ($imageList as $imgIndex => $image) {
                        $imgIndex = $imgIndex + 1;
                        if ($imgIndex > 30 && $countImage > 50) {
                            logWrite('too many item to download break it');
                            break;
                        }
                        $image = explode('data-src=', $image);
                        $image = @preg_match('/http.*?\.(gif|jpg|png|jpeg)/', $image[1], $e);
                        $ext = $e[1];
                        $res = file_get_contents($e[0]);
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
    $_SESSION['data'] = [];
    for ($i = $loopStart; $i <= $loopEnd; $i++) {
        $url = $baseUrl . '&page=' . $i;
        $html = $spider->setUrl($url)
//            ->setReturnCharset()
            ->post();
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $crawler = new DOMXPath($dom);
        $tableDom = $crawler->query('//div[@class="t"][2]/table');
        foreach ($tableDom as $table) {
            $trDom = $crawler->query('//tr[contains(@class,"tr3 t_one tac")]', $table);
            if ($trDom->length) {
                foreach ($trDom as $tr) {
                    $tdDom = $crawler->query('./td', $tr);
                    if ($tdDom->length >= 5) {
                        foreach ($tdDom as $td) {
                            if ($td instanceof DOMElement && $td->attributes->getNamedItem('class')->nodeValue == 'tal') {
                                $title = preg_replace('/\s/', '', $td->textContent);
                                if (strpos($title, '↑') !== false) {
                                    continue;
                                }
                                if (!preg_match("/阿姨|SM|sm|调教|变态|屎|另类/is", $title)) {
//                                if (!preg_match("/十|口|九/is", $title)) {
                                    echo "continue title ". $title . "\n";
                                    continue;
                                }
                                $href = $crawler->query('.//a', $td)->item(0)->attributes->getNamedItem('href')->textContent;
                                $href = $hostInfo['dirname'] . '/' . $href;
                                $_SESSION['data'][] = [
                                    'href' => $href,
                                    'title' => $title
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
    $data = $_SESSION['data'];
    if (!empty($data)) {
        $htmlPath = $htmlStr = '';
        $crawler = null;
        $dictStr = '';
        try {
            foreach ($data as $key => $row) {
                #$itemIndex = 35; // debug
                $html = $spider->setUrl($row['href'])
                    ->setReturnCharset()
                    ->get();
                // div@class='tpc_content do_not_catch'
                preg_match_all("/<h4.*?>.*?<\/h4>|<div class=\"tpc_content do_not_catch\">.*?<\/div>/s", $html, $content);
                $htmlStr = implode('', $content[0]);
                $crawler = new Crawler($htmlStr);
                $sourceTitle = $row['title'];
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
                $dictStr .= '<td class="td-id"><span class="num">' . ($key + 1) . '</span></td>';
                $dictStr .= '<td class="td-title"><a href="' . $sourceLink . '" target="_bank">' . $sourceTitle . '</a></td>';
                $dictStr .= '</tr>';
            }
        } catch (\Exception $e) {
            logWrite('catch Exception on '. ($key + 1));
            logWrite('Exception info : '. $e->getTraceAsString());
        }
        $dictStr = preg_replace('/\<\{CONTENT\}\>/s', $dictStr, $tplFile);
//        $dictStr = mb_convert_encoding($dictStr, 'UTF-8');
        if (!is_dir($saveDictPath)) {
            mkdir($saveDictPath, 0777, true);
        }
        file_put_contents($saveDictPath . 'search' . '.html', $dictStr, FILE_APPEND);
        logWrite('save the search dict success');
    }
}





















