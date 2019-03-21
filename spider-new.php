<?php
ini_set('date.timezone','Asia/Shanghai');
$trigger = "http://sc.ftqq.com/SCU40391T5461103a30dd1dba7bfd3951d600be1e5c7c9b36d2164.send";
$url = 'http://www.yinduowang.com/direct';
set_time_limit(0);
while (1) {
    $opts = array(
        'http' => array(
            'method'=>"GET",
            'header'=>"Content-Type:text/html;charset:UTF-8;Accept-Encoding: gzip, deflate"
        )
    );
    $context  = stream_context_create($opts);
    $content = @file_get_contents($url,false,$context);
    $dom = new \DOMDocument();
    @$dom->loadHTML('<?xml encoding="utf-8" ?>'. $content); // 指定以u8编码加载
    $crawler = new \DOMXPath($dom);
    $list = $crawler->query('//div[@class="in-part2 top50"]/div[@class="list-part"]/div/div[contains(@class, "list")]');
    $result = array();
    $i = 0;
    foreach ($list as $key => $node) {
        $title = $node->childNodes->item(1)->nodeValue;
        $title = explode('VIP', preg_replace('/\s|-/', '', $title));
        $row['title'] = $title[0];
        $temp = $node->childNodes->item(3)->nodeValue;
        $rate = explode("\n", $temp);
        $row['rate'] = preg_replace('/\s/', '', $rate[2]);
        $overRich = $node->childNodes->item(9)->nodeValue;
        preg_match('/\d+/', preg_replace('/\s|,/', '', $overRich), $m);
        $row['over_rich'] = $m[0];
        $result[] = $row;
        $i ++;
    }
    $msg = $text = '';
    $month = 1;
    foreach ($result as $row) {
        $title = preg_match('/\d+/s', $row['title'], $t);
        $title = (int)$t[0];
        if ($title == $month && $row['over_rich'] > 0) {
            $text .= $row['title'] . '上新_剩余'. $row['over_rich']. '|';
            $msg .= '【'. $row['title'] . '】利率【'. $row['rate']. ' 剩余可投【'. $row['over_rich'].'】'. "\n\n";
        }
    }
    if (date('H') >= 9 && date('H') <= 10) {
        $min = 1;
        $max = 44;
        $round = mt_rand($min, $max);
        $limit = $round . 's';
    } else {
        $min = 1;
        $max = 20;
        $round = 60 * mt_rand($min, $max);
        $limit = ($round / 60) . 'm';
    }
    if ($msg) {
        $text = substr($text, 0, -1);
        $query = http_build_query(array(
            'text' => $text,
            'desp' =>  "【limit_". $limit . "】_". $msg
        ));
        $tuiUrl = $trigger . "?". $query;
        file_get_contents($tuiUrl);
    }
    sleep($round);
}
