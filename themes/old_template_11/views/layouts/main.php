<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<?php CmsHtml::head(); ?>
	<meta name="viewport" content="width=device-width">
<?php // CmsHtml::js('/js/main.js'); 
//CmsHtml::css($this->template.'style.css'); 
//CmsHtml::css($this->template.'jquery.fancybox.css'); 
//CmsHtml::css($this->template.'jquery.bxslider.css'); 
Yii::app()->clientScript->registerCssFile('/themes/template_11/css/jquery.bxslider.css');
Yii::app()->clientScript->registerCssFile('/themes/template_11/css/jquery.fancybox.css');
Yii::app()->clientScript->registerCssFile('/themes/template_11/css/style.css');
//Yii::app()->clientScript->registerCoreScript('cookie');  
?>
<?php CmsHtml::js('/js/jquery-migrate-1.2.1.min.js'); ?>
<?php CmsHtml::js('/js/jquery.browser.min.js'); ?>
<?php CmsHtml::js('/js/fancybox/jquery.fancybox-1.3.4.pack.js'); ?>
<?php Yii::app()->clientScript->registerCssFile('/js/fancybox/jquery.fancybox-1.3.4.css'); ?>
<?php CmsHtml::js($this->template.'/js/scripts.js'); ?>
<?php CmsHtml::js($this->template.'/js/jquery.placeholder.js'); ?>
<?php CmsHtml::js($this->template.'/js/jquery.bxslider.min.js'); ?>
<?php //CmsHtml::js($this->template.'/js/jquery.fancybox.pack.js'); ?>
<?php CmsHtml::js($this->template.'/js/jquery.maskedinput-1.3.min.js'); ?>
<?php CmsHtml::js($this->template.'/js/common.js'); ?>
<?php CmsHtml::js('/js/main.js'); ?>
<? if(isset($_GET['page']) || isset($_GET['p'])): ?>
<link rel="canonical" href="<?=$this->createAbsoluteUrl('/').preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI'])?>" />
<? endif; ?>
<script type="text/javascript">
 /*
$(function() {
    $(window).scroll(function() {
    if($(this).scrollTop() != 0) {
        $('#totop').fadeIn();
    } else {
        $('#totop').fadeOut();
    }
});
    
$('#totop').click(function() {
    $('body,html').animate({scrollTop:0},800);
    });
});
*/
</script>
</head>

<body>
   <div class="wrapper">
    <header>
        <div class="page-wrapper__inner">


            
            <div class="h-column clearfix">
<a class="pull-left h-logo" href="/"><img src="<?php echo $this->template; ?>/img/hlogo.jpg" alt="<?php echo CHtml::encode(ModuleHelper::getParam('sitename', true)); ?>" /></a>


                <div class="pull-left h-top-menu">
                        <div class="h-top-m-header">
                            Новосибирск
                        </div>

                        <ul>
                            <li><a href="/dostavka-tcvetov">Доставка</a></li>
                            <li><a href="/akcii">Акции</a></li>
                            <li><a href="/kontakty">Контакты</a></li>
                            <li><a href="/sposoby-oplaty">Оплата</a></li></li>
                        </ul>
                </div>

                <div class="pull-left h-third-col">
                    <div class="h-top-m-header">
                        Как нас найти:
                    </div>
                    <p>
                        м. Площадь Ленина:<br>
                        Фрунзе, 19 <small>(вход с торца)</small><br>
						Без выходных с 8:00-21:00<br>
                    </p>
                </div>

                <div class="pull-right h-pencil">
                    ПРИЁМ ЗАКАЗОВ:
                    <tel>+7 (383) 239-66-75</tel>
					<p class="h-signature">Телефон/WhatsApp:</p>
					<tel>+7 (913) 917-08-71</tel>
                </div>
            </div>

            <nav class="header-row2">
                
				 <?php $this->widget('widget.ShopCart.ShopCart'); ?>
				 <?php $this->widget('widget.ShopCategories.ShopCategories'); ?>
            </nav>


            <div class="h-search-row">
                <div class="pull-right top-search">
                    <form action="javascript:void(0)">
                        <input class="pull-right" type="button" value=""/>
                        <input type="text" placeholder="Поиск цветов"/>

                    </form>
                </div>

                <div class="left-side-tags">
					<?php $this->widget('widget.Filter.Filter'); ?>
                </div>
            </div>



        </div>
    </header>

    <section class="content-sect">
        <div class="page-wrapper__inner">
            
            <?php echo $content; ?>

            <!-- FULLPAGE CONTENT -->

            <div class="three-col-main clearfix">
                <?php $this->widget('widget.newsBlock.newsBlock'); ?>
            </div>

            <div class="svej-cver">Свежие цветы по оптовым ценам в салонах Baza-Roza</div>
            <div class="gray-content">
                    <div class="flowerss"></div>
                <p><i>Самый естественный способ выразить свое уважение, любовь, признательность – подарить букет цветов. Цветы стали универсальным атрибутом любого события. Цветочный букет – лучший вариант, когда вы хотите поздравить близких, сделать комплимент любимому человеку или просто преобразить обстановку в доме. Свежий, оригинальный, эффектный и необычный, он будет долго радовать вас своей красотой, и наполнит любое пространство приятным ароматом, создаст романтическую или праздничную атмосферу.</i></p>
                <p>Мы внимательно относимся к каждому заказу, поэтому если вам необходимо обсудить детали оформления букета или вы не можете оформить заказ онлайн – звоните по телефону 8 (383) 239-66-75 в Новосибирске. </p>
                <p> <i>Вы можете оформить заказ цветов в любое время, так как мы предлагаем быструю и оперативную доставку. Помимо курьерской доставки, возможен самовывоз: вам достаточно подъехать в наш цветочный салон и забрать готовый букет в оговоренные часы.</i></p>
            </div>

            <!-- FULLPAGE CONTENT -->

        </div>
    </section>

<footer>
        <div class="page-wrapper__inner">
            <div class="pull-right f-rrr">
                <div class="f-hef">Каталог</div>
                <ul class="f-list double">
                    <li><a href="/akcii">Акции</a></li>
                    <li><a href="/rozy">Розы</a></li>
                    <li><a href="/bukety">Букеты</a></li>
                    <li><a href="/korziny-i-kompozicii">Корзины</a></li>
               </ul>
            </div>
            <div class="pull-right f-rr">
                <div class="f-hef">Клиентам</div>
                <ul class="f-list">
                    <li><a href="/dostavka-tcvetov">Доставка</a></li>
                    <li><a href="/kak-zakazat-tcvety">  Как заказать?</a></li>
                    <li><a href="/sposoby-oplaty">Способы Оплаты</a></li>
                </ul>
            </div>
            <div class="pull-right f-r">
                <div class="f-hef">Компания</div>
                <ul class="f-list">
                    <li><a href="/kontakty">Контакты</a></li>
                    <li><a href="/news">Новости</a></li>
                </ul>
            </div>
            <div class="pull-right f-city">
                <div class="f-hef">Новосибирск</div>
                <p>Ст. М. Площадь Ленина:<br>
                    Фрунзе, 19<br>
                        (вход с торца здания)
                </p>
            </div>
            <a class="f-logo" href="#">
                Приём заказов:
                <span>+7 (383) 239-66-75</span>
            </a>
        </div>
</footer>

</div> <!-- .wrapper -->

    
<!-- Yandex.Metrika counter --><script type="text/javascript"> (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter34127250 = new Ya.Metrika({ id:34127250, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, trackHash:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");</script><noscript><div><img src="https://mc.yandex.ru/watch/34127250" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->
</body>
</html>
