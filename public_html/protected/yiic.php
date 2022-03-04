<?php
defined('YII_DEBUG') or define('YII_DEBUG',true);

$yiic=dirname(__FILE__).'/../../yii/framework/yiic.php';

if (!is_file($yiic))
    $yiic=dirname(__FILE__).'/../../yii/yiic.php';

if (!is_file($yiic))
    die('Framework not found!');

$config=dirname(__FILE__).'/config/console.php';

require_once($yiic);
