<?php

$cs = Yii::app()->clientScript;
$baseUrl = $this->module->assetsUrl;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/css/login.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/css/elements.css" />
    <?php $cs->registerCoreScript('jquery'); ?>
    <script type="text/javascript" src="<?php echo $baseUrl; ?>/js/main.js"></script>
    
    <script type="text/javascript">
    	$(function(){
            var wrap = $('#wrapper');
            var boxH = $(wrap).height();

            function refreshY() {
                var winH = $(document).height();
                if (winH > boxH) {
                    var toY = winH/2 - boxH/2;
                    $(wrap).css('top', toY);
                }
            }

            $(window).resize(refreshY);
            refreshY();
    	});
    </script>
</head>

<body>
    <div id="wrapper">
        <div id="main">
            <div id="logo"></div>
            <div id="sitename"><?php echo Yii::app()->name; ?></div>
            <div id="line"></div>
            <p id="note">Введите логин и пароль</p>

            <?php echo $content; ?>
        </div>
    </div>

</body>
</html>
