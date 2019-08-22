<?php
ini_set('max_execution_time', 0); // 不限制执行时间
ini_set('memory_limit','512M');
//error_reporting(E_ALL ^E_NOTICE);
error_reporting(0);
set_time_limit(0); // 不限制超时时间
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__. DS. '..'. DS);

define('IS_WIN', strpos(php_uname(), 'Windows') !== false);
$crawRootPath = ROOT . 'crawFiles'. DS;
$saveDictPath = $crawRootPath . 'dict'. DS . 'web' . DS;
$saveImgPath = $crawRootPath . 'dict'. DS . 'img'. DS;
$saveMoviePath = $crawRootPath . 'dict'. DS . 'movie'. DS;
$runTimePath = ROOT . 'runtime'. DS;
$tmplPath = ROOT . 'vendor'. DS . 'tmpl'. DS;
function logWrite($msg) {
    $time = date('Y-m-d H:i:s');
    $msg = $time. " $msg \n";
    echo IS_WIN ? mb_convert_encoding($msg, 'gbk') : $msg;
}

function p($data, $status = null)
{
    echo "<pre>";
    if ($status == 'see') {
        p(get_class_methods($data));
    }
    if ($data == null || $status) {
        var_dump($data);
    } else {
        print_r($data);
    }
    exit("</pre>");
}