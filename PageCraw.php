<?php
/**
 * Created by PhpStorm.
 * User: llf
 * Date: 2018/12/25
 * Time: 16:13
 */
include "./config/config.php";
require './vendor/autoload.php';
require './vendor/lib/Spider.php';

use Symfony\Component\DomCrawler\Crawler;

/*fwrite(STDOUT, iconv('utf-8', 'gbk', "请输入结束页: \n"));
$end = trim(fgets(STDIN));
fwrite(STDOUT, "Hello,$name"); //在终端回显输入*/

$runTime = time();
$crawParams = isset($argv[1]) ? $argv[1] : 'web';
$baseUrl = 'http://private70.ghuws.win/thread0806.php?fid=22';
$loopStart = (isset($argv[2]) && $argv[2]) ? $argv[2] : 1;
$loopEnd = (isset($argv[3]) && $argv[3]) ? $argv[3] : 400;
$filter = (isset($argv[4]) && $argv[4]) ? str_replace(',', '|', trim($argv[4], ',')) : '';
if ($crawParams == 'img') {
    $crawImgType = isset($argv[4]) && $argv[4] ? $argv[4] : 8;
    $filter = isset($argv[5]) && $argv[5] ? str_replace(',', '|', trim($argv[5], ',')) : '';
    $baseUrl = 'http://private70.ghuws.win/thread0806.php?fid='. $crawImgType;
}
$hostInfo = pathinfo($baseUrl);
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

if ($crawParams == 'fanyi') {
    $url = 'https://fanyi.baidu.com/v2transapi?from=en&to=zh';
    $rs = $spider->setUrl($url)
        ->setHeader([
            ':authority' => 'fanyi.baidu.com',
            ':method' => 'POST',
            ':path' => '/v2transapi?from=en&to=zh',
            ':scheme' => 'https',
            'accept' => '*/*',
//            'accept-encoding' => 'gzip, deflate, br',
            'accept-language' => 'zh-CN,zh;q=0.9',
            'content-length' => '128',
            'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'cookie' => 'BIDUPSID=B89ACDE89A40C5001BF6257737FBB1FB; PSTM=1567674158; BAIDUID=33CAECC0F0551EDDC8D3B46AA3D0585C:FG=1; H_WISE_SIDS=130610_126887_136263_100807_134724_135927_132550_134982_120161_133982_136366_132909_136456_136617_131246_136683_136722_132378_131517_118897_118871_118852_118821_118796_107316_132785_136800_136431_136092_133351_129651_136193_132250_128967_135307_133847_132551_135433_135873_134047_136602_131423_133858_135859_134317_136018_110085_136142_134155_127969_131951_135624_136614_135457_127416_136078_136302_136635_136691_133863_134844_136322_136414_100458; H_PS_PSSID=1464_21092_29522_29721_29568_29220; BDORZ=B490B5EBF6F3CD402E515D22BCDA1598; delPer=0; BCLID=11750445540464858929; BDSFRCVID=e50OJeC626U4nyvwuMdCKwLlTFNWO83TH6aoYfCpmNHguzGYCYbMEG0PeU8g0KAbWimyogKK0mOTHv-F_2uxOjjg8UtVJeC6EG0Ptf8g0M5; H_BDCLCKID_SF=tJCJ_KtKJID3qR5gMJ5q-n3HKUrL5t_XbI6y3JjOHJOoDDvNjj3cy4LdjGKqL63XM6cfLl6e0hvb8bR2QxTRXTvy3-Aq54Rk5e7jWlrd5-50sUTJWt5YQfbQ0MoPqP-jW26a-bTEMR7JOpkxhfnxyb5DQRPH-Rv92DQMVU52QqcqEIQHQT3m5-5bbN3ut6T2-DA__I_5JI3P; MCITY=-131%3A; PSINO=2; ZD_ENTRY=baidu; locale=zh; Hm_lvt_64ecd82404c51e03dc91cb9e8c025574=1569832125; Hm_lpvt_64ecd82404c51e03dc91cb9e8c025574=1569832125; to_lang_often=%5B%7B%22value%22%3A%22en%22%2C%22text%22%3A%22%u82F1%u8BED%22%7D%2C%7B%22value%22%3A%22zh%22%2C%22text%22%3A%22%u4E2D%u6587%22%7D%5D; REALTIME_TRANS_SWITCH=1; FANYI_WORD_SWITCH=1; HISTORY_SWITCH=1; SOUND_SPD_SWITCH=1; SOUND_PREFER_SWITCH=1; __yjsv5_shitong=1.0_7_5ecfe819746873d8b9b8d983dcf9e1163a69_300_1569832126409_111.198.24.176_e21d2902; yjs_js_security_passport=568ebfd9b492cba8a3683d4bac0c5d1b891dc879_1569832126_js; from_lang_often=%5B%7B%22value%22%3A%22zh%22%2C%22text%22%3A%22%u4E2D%u6587%22%7D%2C%7B%22value%22%3A%22en%22%2C%22text%22%3A%22%u82F1%u8BED%22%7D%5D',
            'origin' => 'https://fanyi.baidu.com',
            'referer' => 'https://fanyi.baidu.com/',
            'user-agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36',
            'x-requested-with' => 'XMLHttpRequest',
        ])
//        ->setReturnCharset()
        ->post([
            'from' => 'en',
            'to' => 'zh',
            'query' => 'have nice dream',
            'transtype' => 'translang',
            'simple_means_flag' => '3',
            'sign' => '930695.708791',
            'token' => '6526868664eef31a39c95d071e9c1da9',
        ]);


    exit($rs);
}

if ($crawParams == '91') {
//    https://91dizhi-at-gmail-com-0531.w24.rocks/index.php
    $baseUrl = 'https://91dizhi-at-gmail-com-0531.w24.rocks/v.php?category=hot&viewtype=basic&page=2';
    $host = parse_url($baseUrl);
    $host = $host['scheme'] . '://'. $host['host'];

    for ($i = $loopStart; $i <= $loopEnd; $i++) {
        $url = $host . '/v.php?category=hot&viewtype=basic&page=' . $i;
//        $html = file_get_contents($runTimePath. 'debug.html');
        $html = file_get_contents($url);
        $crawler = new Crawler($html);
        $itemList = $crawler->filterXPath('//div[@class="listchannel"]/a');
        foreach ($itemList as $key => $node) {
            $title = $node->getAttribute('title');
            if (IS_WIN) {
                $title = mb_convert_encoding($title, 'gbk');
            }
            $href = $node->getAttribute('href');
            $data[] = [
                'href' => $href,
                'title' => $title
            ];
        }
        logWrite('craw the '. $i . ' page count '. count($data). ' item');
    }

    if (!empty($data)) {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        $htmlPath = $htmlStr = '';
        $crawler = null;
        $dictStr = '';
        try {
            foreach ($data as $key => $row) {
                $html = file_get_contents($row['href']);

                $crawler = new Crawler($html);
                $sourceTitle = $row['title'];
                exit($html);
                p($crawler->filterXPath('//textarea[@class="fullboxtext"]')->count());
                $sourceLink = $crawler->filterXPath('//textarea[@class="fullboxtext"]')->eq(0)->text();
                p($sourceLink);
                $dictStr .= '<tr>';
                $dictStr .= '<td class="td-id"><span class="num">' . ($key + 1) . '</span></td>';
                $dictStr .= '<td class="td-title"><a href="' . $sourceLink . '" target="_bank">' . $sourceTitle . '</a></td>';
                $dictStr .= '</tr>';
            }

        } catch (\Exception $e) {

        }

        exit($dictStr);

        $dictStr = preg_replace('/\<\{CONTENT\}\>/s', $dictStr, $tplFile);
        if (!is_dir($saveDictPath)) {
            mkdir($saveDictPath, 0777, true);
        }
        file_put_contents($saveDictPath . 'search' . '.html', $dictStr);
        logWrite('save the search dict success');
    }
}

if ($crawParams == 'fed') {
    $baseUrl = 'https://www.ffvan.com/video/index/cid/10';
    $host = parse_url($baseUrl);
    $host = $host['scheme'] . '://'. $host['host'];
    $data = [];
    for ($i = $loopStart; $i <= $loopEnd; $i++) {
        $url = $baseUrl . '/p/' . $i;
//        $html = file_get_contents($runTimePath. 'debug.html');
        $html = file_get_contents($url);
        $crawler = new Crawler($html);
        $mainDom = $crawler->filterXPath('//div[@class="detail_right_div"]/ul');
        $itemList = $mainDom->filterXPath('//li');
        foreach ($itemList as $key => $node) {
            $item = $node->getElementsByTagName('img')->item(0);
            $title = $item->getAttribute('title');
            if (IS_WIN) {
                $title = mb_convert_encoding($title, 'gbk');
            }
            $href = $node->getElementsByTagName('a')->item(0)->getAttribute('href');
            $href = $host . $href;
            $data[] = [
                'href' => $href,
                'title' => $title
            ];
        }
        logWrite('craw the '. $i . ' page count '. count($data). ' item');
    }
    if (!empty($data)) {
        echo print_r($data, 1);
        foreach ($data as $key => $row) {
            $html = $spider->setHeader([
                ':authority' => 'www.ffvan.com',
                ':method' => 'GET',
                ':path' => '/video/show/id/68989',
                ':scheme' => 'https',
                'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                'accept-encoding' => 'gzip, deflate, br',
                'accept-language' => 'zh-CN,zh;q=0.9',
                'cookie' => 'PCA=exist; __cfduid=def757ad12302bc3957a6e4672d49cc5f1566463005; jiucao_avs=lmn1hf7iqmkq7s8o4f331365d0; Hm_lvt_5cee5837c78f44327e558552dd935dbe=1566463011,1567667068; Hm_lpvt_5cee5837c78f44327e558552dd935dbe=1567667106',
                'dnt' => '1',
                'if-modified-since' => 'Thu, 05 Sep 2019 07:05:01 GMT',
                'upgrade-insecure-requests' => '1',
                'user-agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36',
            ])->setUrl($row['href'])->post();
            exit($html);
        }
    }
    logWrite('found total items '. count($data));


}

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
        logWrite('the post title '. $postTitle);
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
                    $imagePath = $saveImgPath . $imgTitle . DS;
                    if (IS_WIN) {
                        $imagePath = mb_convert_encoding($saveImgPath . $imgTitle, 'gbk') . DS;
                    }
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
                    $countImage = count($imageList);
                    foreach ($imageList as $imgIndex => $image) {
                        if (!is_dir($imagePath)) break;
                        $imgIndex = $imgIndex + 1;
                        if ($imgIndex > 30 && $countImage > 50) {
                            logWrite('too many item to download break it');
                            break;
                        }
                        $pathInfo = pathinfo($image);
                        $ext = ".". $pathInfo['extension'];
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
                if (IS_WIN) {
                    $title = mb_convert_encoding($title, 'gbk');
                }
                if ($filter && !preg_match('/'. $filter . '/is', $title)) {
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
        logWrite('craw the '. $i . ' page count '. count($data). ' item');
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
        if (IS_WIN) {
            $title = mb_convert_encoding($title[0], 'gbk');
        } else {
            $title = $title[0];
        }
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



















