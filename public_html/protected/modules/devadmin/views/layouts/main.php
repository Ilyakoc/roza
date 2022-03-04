<?php

$cs = Yii::app()->clientScript;
$baseUrl = $this->module->assetsUrl;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link rel="shortcut icon" href="<?php echo $baseUrl; ?>/images/favicon.png" />
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/css/style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/css/elements.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/css/modules.css" />
    <link rel="stylesheet" type="text/css" href="/css/admin.css" />
    <?php $cs->registerCoreScript('jquery'); ?>
    <?php $cs->registerCoreScript('jquery.ui'); ?>
    <?php $cs->registerScriptFile($baseUrl.'/js/admin_main.js'); ?>
    <?php $cs->registerScriptFile($baseUrl.'/js/jquery.simplemodal.1.4.1.min.js'); ?>
</head>
<body>
    <div id="top-line"></div>

    <div id="wrapper">
        <div id="header">
            <div class="left-col">
                <a id="logo" href="<?php echo $this->createUrl('default/index'); ?>" title="Перейти на главную страницу панели администрирования"></a>
                <div id="sitename">
                    <a href="/" target="_blank" title="Перейти на главную страницу сайта"><?php echo Yii::app()->name; ?></a>
                </div>
            </div>
            <div class="right-col">
                <div id="top-menu">
                    <a class="default-button logout-b right" href="<?php echo $this->createUrl('/admin/default/logout'); ?>"><span></span></a>
                    <div class="clr"></div>
                </div>
            </div>
            <div class="clr"></div>
        </div>

        <div id="main">
            <?php echo $content; ?>
        </div>
    </div>
</body>
</html>


 
