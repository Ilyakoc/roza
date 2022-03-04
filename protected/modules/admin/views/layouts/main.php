<?php
/* @var AdminController $this */
/* @var CClientScript $cs */

$cs = Yii::app()->clientScript;
$baseUrl = $this->module->assetsUrl;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link rel="shortcut icon" href="<?php echo $baseUrl; ?>/images/favicon.png" />
    <link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/css/elements.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/css/modules.css" />
    <link rel="stylesheet" type="text/css" href="/css/admin.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/css/style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/css/style_<?php echo $this->skin; ?>.css" />

    <?php $cs->registerCoreScript('jquery'); ?>
    <?php $cs->registerCoreScript('jquery.ui'); ?>
	<?php CmsHtml::js('/js/jquery-migrate-1.2.1.min.js'); ?>
	<?php CmsHtml::js('/js/jquery.browser.min.js'); ?>
    <?php $cs->registerScriptFile($baseUrl.'/js/admin_main.js'); ?>
    <?php $cs->registerScriptFile($baseUrl.'/js/jquery.simplemodal.1.4.1.min.js'); ?>
    <?php $cs->registerScriptFile($baseUrl.'/js/jquery.translit.min.js'); ?>
	<?php Yii::app()->clientScript->registerScriptFile('/js/jquery.flash.js'); ?>
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js" type="text/javascript"></script>
    <![endif]-->
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
                    <a class="default-button create-b left" href="<?php echo $this->createUrl('page/create'); ?>"><span>Создать</span></a>
                    <?php $menu = CmsMenu::getInstance()->adminMenu(); ?>
                    <?php foreach($menu as $menuitem): ?>
                       <?php if(preg_match("/.*shop.*/ui", $menuitem['url'][0])): ?>
                            <a class="<?php if($this->id == "order") { ?> order-button-active <?php } else { ?> default-button <?php } ?> left" href="<?php echo $this->createUrl('order/index'); ?>">Заказы
                                <div id="orderscount" class="notify notifybutton">
                                    <?php echo (int)coreHelper::getNotifies('order'); ?>
                                </div>
                            </a>
                       <?php endif; ?>
                    <?php endforeach; ?>
                    <a class="default-button logout-b right" href="<?php echo $this->createUrl('default/logout'); ?>"><span>Выход</span></a>
                    <a class="default-button settings-b right" href="<?php echo $this->createUrl('default/settings'); ?>"><span>Настройки</span></a>
                    <div class="clr"></div>
                </div>
            </div>
            <div class="clr"></div>
        </div>

        <div id="main">
            <?php echo $content; ?>
        </div>

        <div id="footer">
            <div class="left">
                &copy; <a href="<?php echo $this->skinParam('support_url'); ?>" target="_blank"><?php echo $this->skinParam('support_name'); ?></a>
                &nbsp; <?php echo $this->skinParam('product_name'); ?> <?php readfile(Yii::getPathOfAlias('webroot').DS.'version.txt'); ?>
            </div>
            <div class="right">Служба поддержки клиентов: (383)<noskype></noskype> <?php echo $this->skinParam('support_phone'); ?></div>
            <div class="clr"></div>
        </div>
    </div>
</body>
</html>


 
