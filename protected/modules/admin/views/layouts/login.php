<?php

$baseUrl = $this->module->assetsUrl;
Yii::app()->clientScript->registerCoreScript('jquery');

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link rel="shortcut icon" href="<?php echo $baseUrl; ?>/images/favicon.png" />
    <link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/css/login.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/css/login_<?php echo $this->skin; ?>.css" />
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js" type="text/javascript"></script>
    <![endif]-->
</head>

<body>
    <div class="wrapper">
        <div class="main">
            <div class="logo"></div>
            <div class="sitename"><?php echo Yii::app()->name; ?></div>
            <div class="line"></div>
            <p class="note">Система управления сайтом</p>

            <?php echo $content; ?>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            function toggleTextField(item) {
                setTimeout(function() {
                    if ($(item).val()=='') {
                        $(item).prev().removeClass('hidden');
                    } else {
                        $(item).prev().addClass('hidden');
                    }
                }, 100);

                $(item).bind({
                    focusout: function() {
                        if ($(this).val()=='') {
                            $(this).prev().removeClass('hidden');
                        } else {
                            $(this).prev().addClass('hidden');
                        }
                    },
                    focusin: function() {
                        $(this).prev().addClass('hidden');
                    }
                });

                $(item).prev().click(function() {
                    $(this).addClass('hidden');
                    $(this).next().focus();
                });
            }

            $('#send-login').click(function() {
                $(this).parents('form').submit();
            });

            $('#login-form').bind('submit', function(e) {
                e.preventDefault();
                var form = this;
                $(form).find('.placeholder').addClass('hidden');
                setTimeout(function() {
                    $(form).unbind('submit').submit();
                }, 200);
            });

            toggleTextField('#LoginForm_username');
            toggleTextField('#LoginForm_password');
        });
    </script>
</body>
</html>
