<?php
ob_start();

header('Content-Type: text/html; charset=utf-8');

if(preg_match('/^\/index.php(.*)$/i', $_SERVER['REQUEST_URI'])) {
    header('Location: /');
}
date_default_timezone_set('Asia/Novosibirsk');
ini_set('date.timezone', 'Asia/Novosibirsk');
error_reporting(0);
define('DS', DIRECTORY_SEPARATOR);

$yii = '/var/www/sksseg/data/www/beta.bazaroza.ru/framework/yiilite.php';

//if (!is_file($yii))
//    $yii = dirname(__FILE__).'/../yii1113/yii.php';

if (!is_file($yii))
    $yii = dirname(__FILE__).'/../yii/yiilite.php';

if (!is_file($yii))
    die('Framework not found!');

if (strpos($_SERVER['SERVER_NAME'], 'local') !== false) {
    defined('YII_DEBUG') or define('YII_DEBUG',true);
    $config = dirname(__FILE__).'/protected/config/local.php';
} else
    $config = dirname(__FILE__).'/protected/config/main.php';

define('YII_DEBUG', false);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING); // ~E_WARNING - Fix PHP 7.0 deprecateds.

// error_reporting(E_ALL);

defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);

Yii::createWebApplication($config)->run();

ob_end_flush();
