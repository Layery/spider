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
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\FileCookieJar;
use Symfony\Component\DomCrawler\Crawler;

/*fwrite(STDOUT, iconv('utf-8', 'gbk', "请输入结束页: \n"));
$end = trim(fgets(STDIN));
fwrite(STDOUT, "Hello,$name"); //在终端回显输入*/


class Sucker
{
    protected $client;

    private $driver;

    private $loopStart;

    private $loopEnd;

    private $filter;

    public function __construct()
    {
        global $argv;
        $doAction = 'doImg';
        if (!empty($argv[1])) {
            $this->driver = isset($argv[1]) ? $argv[1] : 'doError';
            $doAction = 'do'. ucfirst($this->driver);
            if (!method_exists($this, $doAction)) {
                exit('can not find this driver');
            }
            if (is_numeric($argv[2]) && is_numeric($argv[3])) {
                $this->loopStart = (isset($argv[2]) && $argv[2]) ? $argv[2] : 1;
                $this->loopEnd = (isset($argv[3]) && $argv[3]) ? $argv[3] : 400;
            }
            if ($this->loopStart || $this->loopEnd) {
                $this->filter = (isset($argv[4]) && $argv[4]) ? str_replace(',', '|', trim($argv[4], ',')) : '';
            }
            $this->crawImgType = isset($argv[4]) && $argv[4] ? $argv[4] : 8;

            $this->client = new Client();
        }
        return call_user_func([$this, $doAction]);
    }

    public function doImg()
    {
        $baseUrl = 'http://private70.ghuws.win/thread0806.php?fid='. $this->crawImgType;
        $data = [];
        $urlInfo = parse_url($baseUrl);
        $hostInfo = pathinfo($baseUrl);
        $urlType = $this->crawImgType;
        for ($i = $this->loopStart; $i <= $this->loopEnd; $i++) {
            $url = $baseUrl . '&page=' . $i;
            logWrite('begin catching the ' . $i . ' page');
//            $client = $this->client->get($url, [
//                'headers' => [
//                    'Host' => 'private70.ghuws.win',
//                    'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
//                    'Upgrade-Insecure-Requests' => '1',
//                    #'accept-encoding' => 'gzip, deflate, br', // 发送编码之后的数据
//                    'accept-language' => 'zh-CN,zh;q=0.9',
//                    'cache-control' => 'no-cache',
//                    'pragma' => 'no-cache',
//                    'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
//                    'upgrade-insecure-requests' => '1',
//                    'referer' => 'https://hs.etet.men/index.php',
//                    'user-agent' => 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Mobile Safari/537.36',
//                    'Cookie' => 'ismob=1; hiddenface=; cssNight=; __cfduid=d9195a93c4a594e3459abb8c62987d9a21558925417; PHPSESSID=sb9ls5v5l3ou823hc24t8m9aj1; UM_distinctid=16aff16dd8511c-0a45c2d4e2a94b-52504913-49a10-16aff16dd8b1d6; 227c9_lastvisit=0%091559626523%09%2Fread.php%3Ftid%3D3542890; CNZZDATA950900=cnzz_eid%3D254784805-1559056130-%26ntime%3D1559628669'
//                ]
//            ]);
//
//            $html = $client->getBody()->getContents();
//            logFile(RUN_TIME_PATH. 'debug.html', $html);
            $html = file_get_contents(RUN_TIME_PATH. 'debug.html');
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
                        $html = '';
                        $imageList = [];
                        $imgTitle = str_replace([',', '，','?', '？', '.', '（', '）', ' ', '　'], '', $row['title']);
                        $itemIndex = $key + 1;
                        #$itemIndex = 76; // debug
                        $imagePath = SAVE_IMG_PATH . $imgTitle . DS;
                        if (IS_WIN) {
                            $imagePath = mb_convert_encoding(SAVE_IMG_PATH . $imgTitle, 'gbk') . DS;
                        }
                        if (!is_dir($imagePath)) {
                            @mkdir($imagePath, 0777, true);
                        } else {
//                            logWrite($imgTitle. ' has download ready continue it');
                            continue;
                        }
//                        logWrite('begin catching the ' . $itemIndex . ' item ' . ' title: ' . $imgTitle);
//                        $html = $this->client->get($row['href'])->getBody()->getContents();
//                        logFile(RUN_TIME_PATH. 'img.html', $html);
                        $html = file_get_contents(RUN_TIME_PATH. 'img.html');
                        $crawler = new Crawler($html);

                        if ($urlType == 7) {
                            $imgListDom = $crawler->filterXPath('//div[@class="tpc_cont"]/img');
                        } else {
                            $imgListDom = $crawler->filterXPath('//div[@class="tpc_cont"]//img');
                        }
                        foreach ($imgListDom as $imageNode) {
                            $imageList[] = $imageNode->getAttribute('ess-data');
                        }
                        $countImage = count($imageList);
                        if ($countImage <= 0) {
                            logWrite('not found image on this page item: '. $itemIndex);
                            continue;
                        } else {
                            logWrite('found '. $countImage . ' images success!');
                        }
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
                            $rs = $this->client->get($image)->getBody()->getContents();
                            p($rs);
                            file_put_contents($downloadFile, $rs);
                            logWrite('save ' . $imgIndex . 'th' . ' success');
                        }
                    }
                }
            } catch (\Exception $e) {
                logWrite('catch Exception on the ' . $i . ' page ' . $itemIndex . ' item');
            }
        }

    }

    public function doError()
    {
        exit('error');
    }

    public static function init()
    {
        return new static();
    }

    public function doWeb()
    {
        $baseUrl = 'http://private70.ghuws.win/thread0806.php?fid=22';
        $hostInfo = pathinfo($baseUrl);
        $tplFile = file_get_contents(TMPL_PATH . 'view.tpl');
        $data = [];
        for ($i = $this->loopStart; $i <= $this->loopEnd; $i++) {
            $url = $baseUrl . '&page=' . $i;
            $client = $this->client->get($url, [
                'headers' => [
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
                ]
            ]);
            $html = $client->getBody()->getContents();
////            $rs = logFile(RUN_TIME_PATH. 'debug.html', $html);
//            $html = file_get_contents(RUN_TIME_PATH. 'debug.html');
            $crawler = new Crawler($html);
            $mainList = $crawler->filterXPath('//div[@class="list t_one"]');
            foreach ($mainList as $node) {
                if ($node->getAttribute('class')) {
                    $title = preg_replace('/\s/', '', $node->textContent);
                    if (strpos($title, '↑') !== false || strpos($title, '■■■') !== false) {
                        continue;
                    }
                    preg_match('/(\[.*?\d+\:\d+\])/is', $title, $m);
                    $title = $m[0];
                    if ($this->filter && !preg_match('/'. $this->filter . '/is', $title)) {
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
        if (!empty($data)) {
            $htmlPath = $htmlStr = '';
            $crawler = null;
            $dictStr = '';
            try {
                foreach ($data as $key => $row) {
                    unset($html);
                    $client = $this->client->get($row['href']);
                    $html = $client->getBody()->getContents();
//                    $html = file_get_contents(RUN_TIME_PATH. 'detail.html');
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
            if (!is_dir(SAVE_DICT_PATH)) {
                mkdir(SAVE_DICT_PATH, 0777, true);
            }
            file_put_contents(SAVE_DICT_PATH . 'search' . '.html', $dictStr);
            logWrite('save the search dict success');
        }
    }
}

Sucker::init()->doImg();


DIE;

$runTime = time();
$crawParams = isset($argv[1]) ? $argv[1] : 'web';
$baseUrl = 'http://private70.ghuws.win/thread0806.php?fid=22';
if (is_numeric($argv[2]) || is_numeric($argv[3])) {
    $loopStart = (isset($argv[2]) && $argv[2]) ? $argv[2] : 1;
    $loopEnd = (isset($argv[3]) && $argv[3]) ? $argv[3] : 400;
}
$filter = (isset($argv[2]) && $argv[2]) ? str_replace(',', '|', trim($argv[2], ',')) : '';
if ($loopStart || $loopEnd) {
    $filter = (isset($argv[4]) && $argv[4]) ? str_replace(',', '|', trim($argv[4], ',')) : '';
}

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

//$crawParams = 'debug';
if ($crawParams == 'debug') {

    // ffmpeg -f concat -i inputs.txt out.flv
    $cmd = '';
    $cmd .= 'D:/XiaoWanToolBox/tools/ffmpeg.exe -f concat -safe 0 -i E:/www/spider/crawFiles/dict/movie/out.txt -c copy E:/www/spider/crawFiles/dict/movie/out.mp4';
    exec($cmd);
    die;


    $arr = range(0, 403);
    foreach ($arr as $v) {
        $data = "file E:/www/spider/crawFiles/dict/movie/" . $v. ".ts". "\n";
        file_put_contents($saveMoviePath. 'out.txt', $data, FILE_APPEND);
    }
}



if ($crawParams == 'yase') {
    $baseUrl = 'https://j.yaseh4.com/search/?type=video&keyword='. $filter;
    $baseUrl = 'https://j.yasejj2.com/video/view/984405';
    $result = file_get_contents($baseUrl);
    file_put_contents($runTimePath. 'result.html', $result);
    $host = parse_url($baseUrl);
    $host = $host['scheme'] . '://'. $host['host'];
    $crawler = new Crawler($result);
    $itemList = $crawler->filterXPath('//li[@class="video-item space-sm"]/div[@class="white"]');
    $data = $itemList->each(function (Crawler $node, $i) use ($host) {
        $href = $node->filterXPath('//div[@class="video-thumb"]/div')->attr('data-href');
        $href = $host . $href;
        $title = $node->filterXPath('//div[@class="video-item-title"]/a')->text();
        return ['href' => $href, 'title' => $title];
    });
    foreach ($data as $index => $row) {
        $nowTitle = mb_convert_encoding($row['title'], 'gbk');
        $itemID = (int) basename($row['href']);
        $api = $host . '/api/video/player_domain?id='. $itemID;
        $apiResult = json_decode(file_get_contents($api), 1);
        if ($apiResult['code'] == -1) {
            logWrite($apiResult['msg']);
            continue;
        }
        $apiResultUrl = $apiResult['data'];
        $apiPathInfo = pathinfo($apiResultUrl);
        $apiResult = parse_url($apiResultUrl);
        $apischeme = $apiResult['scheme'];
        $apischeme = 'http';
        $apiHost = $apischeme . "://". $apiResult['host'];
        $master = file_get_contents($apiResultUrl);
        $playlist = explode("\n", $master);
        // 拼接ts文件
        if (empty($playlist[2])) {
            logWrite('can not find ts config');
            continue;
        }

        logWrite($row['title']. " begin catching");

        $tsUrl = $apiPathInfo['dirname'] .'/'. $playlist[2];
        $tsResult = file_get_contents($tsUrl);
        preg_match_all('/\/\d+.*?\n/', $tsResult, $m);
        if (empty($m[0])) continue;
        $tsArr = range(0, count($m[0])-1);
        $cmd = "copy /b ";
        foreach ($m[0] as $key => $val) {
            $url = rtrim($apiHost . $val, "\n");
            $temp = file_get_contents($url);
            file_put_contents($saveMoviePath . $key . '.ts', $temp);
            $cmd .= $saveMoviePath. $key. ".ts+";
        }
        $cmd = rtrim($cmd, '+');
        $cmd .= " ". $saveMoviePath. $nowTitle. '.mp4';
        exec($cmd);
        // exec('del /s/q/f "'. $saveMoviePath. '*.ts' .'"');
    }
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
//        logWrite('the post title '. $postTitle);
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
//                        logWrite($imgTitle. ' has download ready continue it');
                        continue;
                    }
//                    logWrite('begin catching the ' . $itemIndex . ' item ' . ' title: ' . $imgTitle);
                    $html = $spider->setUrl($row['href'])->get();
                    #$html = file_get_contents($runTimePath. $urlType . '-child.html');
                    $crawler = new Crawler($html);
                    if ($urlType == 7) {
                        $imgListDom = $crawler->filterXPath('//div[@class="tpc_cont"]/img');
                    } else {
                        $imgListDom = $crawler->filterXPath('//div[@class="tpc_cont"]//img');
                    }
                    foreach ($imgListDom as $imageNode) {
                        $imageList[] = $imageNode->getAttribute('data-src');
                    }
                    $countImage = count($imageList);
                    if ($countImage <= 0) {
                        logWrite('not found image on this page item: '. $itemIndex);
                        continue;
                    } else {
                        logWrite('found '. $countImage . ' images success!');
                    }
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



















