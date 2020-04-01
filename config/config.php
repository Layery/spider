<?php
//date_default_timezone_set('Asia/Shanghai');
ini_set('max_execution_time', 0); // 不限制执行时间
ini_set('memory_limit','512M');
//error_reporting(E_ALL ^E_NOTICE);
error_reporting(0);
set_time_limit(0); // 不限制超时时间
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__. DS. '..'. DS);

define('IS_WIN', strpos(php_uname(), 'Windows') !== false);

define('CRAW_ROOT_PATH', ROOT . 'crawFiles'. DS);
define('SAVE_DICT_PATH', CRAW_ROOT_PATH. 'dict'. DS . 'web'. DS);
define('SAVE_IMG_PATH', CRAW_ROOT_PATH. 'dict'. DS . 'img'. DS);
define('SAVE_MOVIE_PATH', CRAW_ROOT_PATH. 'dict'. DS . 'movie'. DS);
define('RUN_TIME_PATH', ROOT. 'runtime'. DS);
define('TMPL_PATH', ROOT. 'vendor'. DS. 'tmpl'. DS);
define('COOKIE_PATH',  ROOT. 'cookie'. DS);

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
    if (empty($data) || $status) {
        var_dump($data);
    } else {
        print_r($data);
    }
    exit("</pre>");
}
