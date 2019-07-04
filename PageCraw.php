<?php
/**
 * Created by PhpStorm.
 * User: llf
 * Date: 2018/12/25
 * Time: 16:13
 */
include "./config.php";
require './vendor/autoload.php';
require './vendor/lib/Spider.php';

use Symfony\Component\DomCrawler\Crawler;


//    fwrite(STDOUT, iconv('utf-8', 'gbk', "请输入结束页: \n"));
//    $end = trim(fgets(STDIN));
//    fwrite(STDOUT, "Hello,$name"); //在终端回显输入
$crawParams = isset($argv[1]) ? $argv[1] : 'web';
$search = 'https://yandex.ru/search/?text=%E8%8D%89%E6%A6%B4%E7%A4%BE%E5%8C%BA';
$baseUrl = '';
if ($crawParams == 'web') {
    $baseUrl = 'http://private70.ghuws.win/thread0806.php?fid=22';
} else if ($crawParams == 'img') {
    $baseUrl = 'http://private70.ghuws.win/thread0806.php?fid=8'; // 新时代
    $baseUrl = 'http://private70.ghuws.win/thread0806.php?fid=16';
    $baseUrl = 'http://private70.ghuws.win/thread0806.php?fid=7'; // 技术讨论
}
//http://cl.wpio.xyz/htm_mob/22/1903/3469154.html
$hostInfo = pathinfo($baseUrl);

$loopStart = (isset($argv[2]) && $argv[2]) ? $argv[2] : 1;
$loopEnd = (isset($argv[3]) && $argv[3]) ? $argv[3] : 400;
$filter = (isset($argv[4]) && $argv[4]) ? str_replace(',', '|', trim($argv[4], ',')) : '';

$runTime = time();
$tplFile = file_get_contents($tmplPath . 'view.tpl');
$spider = new Spider();
$spider->setHeader([
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
    ]);



if ($crawParams == 'img') {
    $data = [];
    $urlInfo = parse_url($baseUrl);
    $urlType = substr($urlInfo['query'], 4);
    for ($i = $loopStart; $i <= $loopEnd; $i++) {
        $url = $baseUrl . '&page=' . $i;
        logWrite('begin catching the ' . $i . ' page');
        $html = $spider->setUrl($url)
                ->get();
        #$html = file_get_contents($runTimePath . 'fid='. $urlType. '.html');
        $crawler = new Crawler($html);
        $postTitle = $crawler->filterXPath('//title')->text();
        try {
            logWrite('begin parse the ' . $i . ' page');
            $mainList = $crawler->filterXPath('//div[contains(@class,"list t_one")]');
            foreach ($mainList as $node) {
                if ($node->getAttribute('class')) {
                    $title = preg_replace('/\s/', '', $node->textContent);
                    if (strpos($title, '↑') !== false || strpos($title, '■■■') !== false) continue;
                    if (!preg_match("/\(\d+[pP]\)|\[\d+[pP]\]|［\d+[pP]］|图|精品/is", $title)) continue;
                    if ($urlType == 8 && strpos($title, '歐')) continue;
                    $title = $node->getElementsByTagName('a')->item(0)->nodeValue;
                    $href = str_replace(["'", ";"], "", $node->getAttribute('onclick'));
                    $href = substr($href, 16);
                    $href = $hostInfo['dirname'] . '/'. $href;
                    $data[] = [
                        'href' => $href,
                        'title' => $title
                    ];
                }
            }
            $totalItem = count($data);
            logWrite('parse the ' . $i . ' page success count ' . $totalItem . ' item');
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
                    if (!is_dir($imagePath)) {
                        @mkdir($imagePath, 0777, true);
                    } else {
                        logWrite($imgTitle. ' has download ready continue it');
                        continue;
                    }
                    logWrite('begin catching the ' . $itemIndex . ' item ' . ' title: ' . $imgTitle);
                    $html = $spider->setUrl($row['href'])->get();
                    #$html = file_get_contents($runTimePath. $urlType . '-child.html');
                    $crawler = new Crawler($html);
                    if ($urlType == 7) {
                        $imgListDom = $crawler->filterXPath('//div[@class="tpc_cont"]/img');
                    } else {
                        $imgListDom = $crawler->filterXPath('//div[@class="tpc_cont"]//input');
                    }
                    foreach ($imgListDom as $imageNode) {
                        $imageList[] = $imageNode->getAttribute('data-src');
                    }

                    if ($continueStatus) continue;
                    $countImage = count($imageList);
                    foreach ($imageList as $imgIndex => $image) {
                        $imgIndex = $imgIndex + 1;
                        if ($imgIndex > 30 && $countImage > 50) {
                            logWrite('too many item to download break it');
                            break;
                        }
                        $ext = substr($image, -4);
                        $downloadFile = $imagePath . $imgIndex . $ext;
                        $spider->setUrl($image)->download($downloadFile);
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
        $html = $spider->setUrl($url)->get();
        $crawler = new Crawler($html);
        $mainList = $crawler->filterXPath('//div[@class="list t_one"]');
        foreach ($mainList as $node) {
            if ($node->getAttribute('class')) {
                $title = preg_replace('/\s/', '', $node->textContent);
                if (strpos($title, '↑') !== false || strpos($title, '■■■') !== false) {
                    continue;
                }
                preg_match('/\[.*?\d+\:\d+\]/is', $title, $m);
                $title = $m[0];
                if ($filter && !preg_match('/'. $filter . '/is', mb_convert_encoding($title, 'gbk'))) {
                    continue;
                }
                $href = str_replace(["'", ";"], "", $node->getAttribute('onclick'));
                $href = substr($href, 16);
                $href = $hostInfo['dirname'] . '/'. $href;
                $data[] = [
                    'href' => $href,
                    'title' => $title
                ];
            }
        }
    }
    logWrite('found total items '. count($data));
    if (!empty($data)) {
        $htmlPath = $htmlStr = '';
        $crawler = null;
        $dictStr = '';
        try {
            foreach ($data as $key => $row) {
                $html = $spider->setUrl($row['href'])
                    ->setFollowLocation()
                    ->get();
                $crawler = new Crawler($html);
                $sourceTitle = $row['title'];
                $sourceLink = 'src=http://www.baidu.com';
                $aCount = $crawler->filterXPath('//div[contains(@class,"tpc_cont")]/a[2]')->count();
                if ($aCount) {
                    $sourceLink = $crawler->filterXPath('//div[contains(@class,"tpc_cont")]/a[2]')->attr('onclick');
                    $sourceLink = explode('src=', $sourceLink);
                    $sourceLink = trim($sourceLink[1], '\'');
                } else {
                    $sourceLink = $crawler->filterXPath('//div[contains(@class,"tpc_cont")]')->text();
                    $sourceLink = preg_match("~http[s]?://([\w-]+\.)+[\w-]+(/[\w- ./?%&=]*)~", $sourceLink, $m);
                    $sourceLink = $sourceLink[0];
                }
                $sourceList[] = ['sourceLink' => $sourceLink, 'sourceTile' => $sourceTitle];
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
        file_put_contents($saveDictPath . 'search' . '.html', $dictStr);
        logWrite('save the search dict success');
    }
}

if ($crawParams == 'down') {
    $html = file_get_contents($saveDictPath. 'search.html');
    preg_match_all('/\<a\s+.*?\>.*?\<\/a\>/is', $html, $a);
    foreach ($a[0] as $key => $row) {
        preg_match('/(?<=href=\").*?(?=\"\s+target)/', $row, $href);
        preg_match('/\[.*?\d+\:\d+\]/is', $row, $title);
        $title = mb_convert_encoding($title[0], 'gbk');
        $title = str_replace(['|', '"','<', '>', ':', '[', ']', '【', '】'], ['','','','','-'], $title);
        $html = file_get_contents($href[0]);
        preg_match('/video\:+\'.*?\.mp4|video_url\:\s+\'.*?\.mp4/', $html, $videoUrl);
        if (empty($videoUrl)) {
            logWrite('could not found item continue it');
            continue;
        }
        $videoUrl = explode("'", $videoUrl[0]);
        $videoUrl = $videoUrl[1];
        logWrite('begin download file '. $title);
        $spider->setUrl($videoUrl)->download($saveMoviePath . $title . '.mp4');
    }
}



















