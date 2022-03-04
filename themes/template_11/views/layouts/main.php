<!DOCTYPE html>
<html lang="ru">
<head>
	<?php CmsHtml::head(); ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  	<meta name="geo.placename" content="улица Фрунзе, 19, Новосибирск, Россия, 630091" />
    <meta name="geo.position" content="55.7174590;37.5113430" />
    <meta name="geo.region" content="RU-город Новосибирск" />
    <meta name="ICBM" content="55.03792806968415, 82.9279395" />
<?php // CmsHtml::js('/js/main.js');
//CmsHtml::css($this->template.'style.css');
//CmsHtml::css($this->template.'jquery.fancybox.css');
//CmsHtml::css($this->template.'jquery.bxslider.css');
Yii::app()->clientScript->registerCssFile('/themes/template_11/css/jquery.bxslider.css');
// Yii::app()->clientScript->registerCssFile('/themes/template_11/css/jquery.fancybox.css');
Yii::app()->clientScript->registerCssFile('/dist/jquery.fancybox.min.css');
Yii::app()->clientScript->registerCssFile('/themes/template_11/css/slick.css');
Yii::app()->clientScript->registerCssFile('/themes/template_11/css/style.css');

//Yii::app()->clientScript->registerCoreScript('cookie');
?>
<?php CmsHtml::js('/js/jquery-migrate-1.2.1.min.js'); ?>
<?php CmsHtml::js('/js/jquery.browser.min.js'); ?>
<?php CmsHtml::js('/dist/jquery.fancybox.min.js'); ?>
 <!-- <?php Yii::app()->clientScript->registerCssFile('/js/fancybox/jquery.fancybox-1.3.4.css'); ?> -->
<?php CmsHtml::js($this->template.'/js/slick.min.js'); ?>
<?php CmsHtml::js($this->template.'/js/scripts.js'); ?>
<?php CmsHtml::js($this->template.'/js/jquery.placeholder.js'); ?>
<?php CmsHtml::js($this->template.'/js/jquery.bxslider.min.js'); ?>
<?php //CmsHtml::js($this->template.'/js/jquery.fancybox.pack.js'); ?>
<?php CmsHtml::js($this->template.'/js/jquery.maskedinput-1.3.min.js'); ?>
<?php CmsHtml::js($this->template.'/js/common.js'); ?>

<?php CmsHtml::js('/js/main.js'); ?>
<?php if((isset($_GET['p']) || isset($_GET['page'])) && !isset($_GET['min']) && !isset($_GET['max'])): ?>
	<link rel="canonical" href="<?=$this->createAbsoluteUrl('/').preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI'])?>" />
<?php endif; ?>

<link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i&display=swap&subset=cyrillic" rel="stylesheet">

<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

<script>
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
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-164795922-1', 'auto');
ga('send', 'pageview');
</script>
<style>
.slider-top-wrapp .slick-track .slick-current{
	height: 425px;
}
</style>
</head>

<body>
   <div class="wrapper">

 <div class="c-wrapper__flex">

	<header>
		<div class="page-wrapper__inner">

			<div class="header-mobile">

				<div class="header-mobile-inner">
					<div class="header-mobile-humburger">

					</div>

					<div class="header-mobile-cart">
						<?php $this->widget('widget.ShopCart.ShopCart', ['view'=>'mobile']); ?>
					</div>
				</div>


				<div class="header-mobile-dropdown">
					<ul>
						<li><a href="/usloviia-dostavki">Доставка</a></li>
						<li><a class="active" href="/akcii">Акции</a></li>
						<li><a href="/kontakty">Контакты</a></li>
						<li><a href="/sposoby-oplaty">Оплата</a></li>
					</ul>
				</div>
			</div>

			<div class="h-column clearfix">
			<div class="h-logo">

				<a href="/">
					<img src="<?php echo $this->template; ?>/img/logo.svg" alt="<?php echo CHtml::encode(ModuleHelper::getParam('sitename', true)); ?>" />
				</a>

				<div class="social-links">
					<!--<span>Мы в соцсетях:</span>
					<a href="<?= ModuleHelper::getParam('vk', true) ?>" target="_blank">
						<img src="/images/vk.svg" alt="">
					</a>
					<a href="<?= ModuleHelper::getParam('instagram', true) ?>" target="_blank">
						<img src="/images/instagram.svg" alt="">
					</a>-->
				</div>
			</div>

				<div class="h-top-menu">
						<div class="h-top-m-header">
							Москва<br>Санкт-Петербург<br>Екатеринбург
						</div>

						<ul>
							<li><a href="/usloviia-dostavki">Доставка</a></li>
							<li><a class="active" href="/akcii">Акции</a></li>
							<li><a href="/kontakty">Контакты</a></li>
							<li><a href="/sposoby-oplaty">Оплата</a></li>
							<li><a href="/O-kompanii">О компании</a></li>
						</ul>
				</div>

				<?php if (false): ?>
					<div class="h-third-col">
						<div class="h-top-m-header">
							Как нас найти:
						</div>
						<p>
							м. Площадь Ленина:<br>
							Фрунзе, 19 <small>(вход с торца)</small><br>
							Без выходных с 8:00-21:00<br>
						</p>
					</div>
				<?php endif ?>

				<div class="h-pencil">
					г.Москва:
					<div class="tel">+7 (495) 859-31-94</div>

					г.Санкт-Петербург:
					<div class="tel">+7 (812) 385-57-56</div>

					г.Екатеринбург:
					<div class="tel">+7 (343) 288-71-08</div>
				</div>
			</div>

			<nav class="header-row2">
				<?php if (/*!Yii::app()->user->isGuest && */ $button = Button::model()->findByAttributes(['active' => 1], ['order'=>'id DESC'])): ?>
					<a href="<?= $button->link ?>" class="btn-celebration">
						<img src="/images/button/<?= $button->preview ?>" alt="" class="<?= $button->image_class ?>">
						<span><?= $button->title ?></span>
					</a>
				<?php else: ?>
					<a href="<?= ModuleHelper::getParam('header_link', true) ?>" class="header-nav-link">
						<?php if ($file = ModuleHelper::getParam('file_top_button', true)): ?>
							<img src="<?= ResizeHelper::resize(Yii::app()->params['uploadSettingsPath'] . $file, 98, false) ?>" alt="">
						<?php endif ?>
						<span><?= ModuleHelper::getParam('header_link_text', true) ?></span>
					</a>
				<?php endif; ?>
				<?php $this->widget('widget.ShopCart.ShopCart'); ?>
				<?php $this->widget('widget.ShopCategories.ShopCategories', ['disabled'=>[]]); ?>
			</nav>

			<?php if (YiiHelper::isController($this, 'shop')): ?>
			<div class="h-search-row">
				<div class=" top-search">
					<form action="<?=$this->createUrl('search/index')?>">
						<input class="pull-right" type="submit" value=""/>
						<?$this->widget('CAutoComplete',
                            array(
                                'model'=>'Search',
                                'name'=>'q',
                                'url'=>array('/search/autocomplete'),
                                'minChars'=>2,
                                'htmlOptions'=>array('placeholder'=>"Поиск цветов")
                            )
                        )?>
					</form>
				</div>

				<div class="left-side-tags">
					<?php $this->widget('widget.Filter.Filter'); ?>
				</div>
			</div>
			<? endif; ?>


		</div>
	</header>

	<section class="content-sect">
		<div class="page-wrapper__inner">

			<?php echo $content; ?>

			<!-- FULLPAGE CONTENT -->

			<div class="three-col-main clearfix">

				<?php if ($this->isIndex()): ?>
					<div class="ns-wrap">

						<div class="category-description" itemscope="">

							<p>Рассказать о чувствах человеку можно по-разному, цветы помогут выразить то, что трудно передать словами. Букет может передать уважение, высказать восхищение, рассказать о любви, поблагодарить и даже заинтриговать. Чтобы порадовать своих родных и близких, просто закажите цветочную композицию в нашем интернет-магазине.</p>
							<h2>Лучшие условия для наших клиентов</h2>
							<ul>
								<li>Доступные цены. В каждом разделе каталога вы можете выбирать в рамках доступного бюджета</li>
								<li>Большой ассортимент. У нас в магазине покупатель всегда найдет много растений, которые соединятся в оригинальный букет.</li>
								<li>Доставка в любую точку Москвы, Санкт-Петербурга и Екатеринбурга с 8 до 22 часов. Заказ можно сделать заранее, указав точное время и адрес.</li>
								<li>Удобная связь. Поддерживать общение с нами можно по телефону или через приложение WhatsApp</li>
								<li>Услуги флориста. Наши специалисты соберут интересную композицию, опираясь на ваши пожелания</li>
							</ul>
							<ul>
								<li>Свежие растения. Наши поставщики регулярно привозят свежий товар, поэтому букеты из нашего магазина будет долго радовать вас своей красотой</li>
								<li>Удобные способы оплаты. Оплатить заказ можно онлайн или денежным переводом, также можно рассчитаться через терминал банка или наличными</li>
							</ul>
							<h2>Тематические букеты для вашего праздника</h2>
							<p>В нашем интернет-магазине для любого события всегда можно подобрать недорогой букет:</p>
							<ul>
								<li>День рождения &ndash; от скромной композиции до роскошного букета на юбилей из 25, 40 или 50 цветов.</li>
								<li>Свадьба &ndash; букеты для невесты, ее подружек, бутоньерки для жениха, элементы для декора праздничного зала</li>
								<li>8 марта и 23 февраля &ndash; яркие весенние тюльпаны или строгий бамбук, подберем идеальное сочетание</li>
								<li>14 февраля &ndash; романтические признания, воплощенные в сердце из роз</li>
								<li>Детский праздник &ndash; игрушка из хризантем или роз станет очень оригинальным сюрпризом</li>
								<li>Новый год &ndash; небольшая декоративная ель будет к месту в любом доме</li>
								<li>1 сентября &ndash; необычные решения для самого важного школьного дня</li>
							</ul>



						</div>
												<div class="ns-category-list">
							<?php/*
								$category = [
									[
										'name'=> 'Розы',
										'img'=> 'ns-sb-1.jpg',
										'url'=> ''
									],
									[
										'name'=> 'Хризантемы',
										'img'=> 'ns-sb-2.jpg',
										'url'=> ''
									],
									[
										'name'=> 'Герберы',
										'img'=> 'ns-sb-3.jpg',
										'url'=> ''
									],
									[
										'name'=> 'ТЮЛЬПАНЫ',
										'img'=> 'ns-sb-4.jpg',
										'url'=> ''
									],
									[
										'name'=> 'ЛИЛИИ',
										'img'=> 'ns-sb-5.jpg',
										'url'=> ''
									],
									[
										'name'=> 'ОРХИДЕИ',
										'img'=> 'ns-sb-6.jpg',
										'url'=> ''
									],
									[
										'name'=> 'Тематические букеты',
										'img'=> 'ns-sb-7.jpg',
										'url'=> ''
									],
									[
										'name'=> 'Корзины с цветами',
										'img'=> 'ns-sb-8.jpg',
										'url'=> ''
									],
									[
										'name'=> 'Розы в шляпной коробке и т.д.',
										'img'=> 'ns-sb-9.jpg',
										'url'=> ''
									],
								];

								foreach ($category as $key => $value) {

									?>

										<div class="ns-category-item-wrap">
											<div class="ns-category-item">
												<span class="ns-category-item-img">
													<img src="<?=$this->template?>/img/<?echo $value['img'];?>" alt="" />
												</span>
												<span class="ns-category-item-name">
													<span><?echo $value['name'];?></span>
												</span>
											</div>
										</div>
									<?
								}*/
							?>
						</div>

						<div class="ns-roses-list">
							<?/*
								$roses = [
									[
										'name'=>'Розы <br>поштучно',
										'url'=>''
									],
									[
										'name'=>'Розы в шляпной <br>коробке',
										'url'=>''
									],
									[
										'name'=>'Букеты из <br>25, 31, 55 и 101 розы',
										'url'=>''
									],
									[
										'name'=>'Кустовые <br>розы',
										'url'=>''
									]
								];

								foreach ($roses as $key => $value) {
									?>
										<div class="ns-roses-item-wrap">
											<div class="ns-roses-item">
												<p><?echo $value['name']?></p>
											</div>
										</div>
									<?
								}
							*/?>
						</div>


					</div>
				<?php endif ?>


				<?php #$this->widget('widget.newsBlock.newsBlock'); ?>
			</div>
<? /* ?>
			<div class="svej-cver">Свежие цветы по оптовым ценам в салонах Baza-Roza</div>
			<div class="gray-content">
					<div class="flowerss"></div>
				<p><i>Самый естественный способ выразить свое уважение, любовь, признательность – подарить букет цветов. Цветы стали универсальным атрибутом любого события. Цветочный букет – лучший вариант, когда вы хотите поздравить близких, сделать комплимент любимому человеку или просто преобразить обстановку в доме. Свежий, оригинальный, эффектный и необычный, он будет долго радовать вас своей красотой, и наполнит любое пространство приятным ароматом, создаст романтическую или праздничную атмосферу.</i></p>
				<p>Мы внимательно относимся к каждому заказу, поэтому если вам необходимо обсудить детали оформления букета или вы не можете оформить заказ онлайн – звоните по телефону 8 (383) 239-66-75 в Новосибирске. </p>
				<p> <i>Вы можете оформить заказ цветов в любое время, так как мы предлагаем быструю и оперативную доставку. Помимо курьерской доставки, возможен самовывоз: вам достаточно подъехать в наш цветочный салон и забрать готовый букет в оговоренные часы.</i></p>
			</div>
<? /**/ ?>
			<!-- FULLPAGE CONTENT -->

		</div>
	</section>

</div>


<div class="footer-top">
	<div class="page-wrapper__inner">
		<div class="seo-link">
			<div class="seo-link-col">
				<div class="seo-link__heading">Цветы:</div>
				<div class="seo-link-columns">
					<?php foreach (FooterLink::model()->findAll(['condition' => 'column_id = 1', 'order' => 'sort ASC, id ASC']) as $footerLink): ?>
						<div class="columns-item"><?= CHtml::link($footerLink->title, $footerLink->link ? : Yii::app()->createUrl('/shop/category', ['id' => $footerLink->category_id])) ?></div>
					<?php endforeach ?>
				</div>
			</div>
			<div class="seo-link-col">
				<div class="seo-link__heading">Поводы:</div>
				<div class="seo-link-columns">
					<?php foreach (FooterLink::model()->findAll(['condition' => 'column_id = 2', 'order' => 'sort ASC, id ASC']) as $footerLink): ?>
						<div class="columns-item"><?= CHtml::link($footerLink->title, $footerLink->link ? : Yii::app()->createUrl('/shop/category', ['id' => $footerLink->category_id])) ?></div>
					<?php endforeach ?>
				</div>
			</div>
			<div class="seo-link-col">
				<div class="seo-link__heading">Кому:</div>
				<div class="seo-link-columns">
					<?php foreach (FooterLink::model()->findAll(['condition' => 'column_id = 3', 'order' => 'sort ASC, id ASC']) as $footerLink): ?>
						<div class="columns-item"><?= CHtml::link($footerLink->title, $footerLink->link ? : Yii::app()->createUrl('/shop/category', ['id' => $footerLink->category_id])) ?></div>
					<?php endforeach ?>
				</div>
			</div>
		</div>
	</div>
</div>
<footer>
        <div class="page-wrapper__inner">
            <div class="f-logo">
                <a href="javascript:;">
                    <img src="<?=$this->template?>/img/logo.svg" alt="" />
                </a><!--
                <div class="">
                    Приём заказов:
                    <span>+7 (383) 239-66-75</span>
                </div>-->
            </div>
            <div class="f-city">
                <div class="f-hef">г.Москва</div>
                <span>+7 (495) 859-31-94</span><br>
				<div class="f-hef">г.Санкт-Петербург</div>
                <span>+7 (812) 385-57-56</span><br>
				<div class="f-hef">г.Екатеринбург</div>
                <span>+7 (343) 288-71-08</span>
            </div>
            <div class="footer-link-block">
                <div class="f-r">
                    <div class="f-hef">Компания</div>
                    <ul class="f-list">
                        <li><a href="/kontakty">Контакты</a></li>
                        <?/*?><li><a href="/news">Новости</a></li><?*/?>
                        <li><a href="<?= Yii::app()->createUrl('/site/page', ['id' => 16]) ?>">Корпоративным клиентам</a></li>
                        <li><a href="/O-kompanii">О компании</a></li>
                    </ul>
                </div>
                <div class="f-rr">
                    <div class="f-hef">Клиентам</div>
                    <ul class="f-list">
                        <li><a href="<?= Yii::app()->createUrl('/site/page', ['id' => 8]) ?>">Доставка</a></li>
                        <li><a href="/kak-zakazat-tcvety">Как заказать?</a></li>
                        <li><a href="<?= Yii::app()->createUrl('/site/page', ['id' => 15]) ?>">Бизнес-флористика</a></li>
                    </ul>
                </div>
                <div class="f-rrr">
                    <div class="f-hef">Каталог</div>
                    <ul class="f-list double">
                        <li><a href="/akcii">Акции</a></li>
                        <li><a href="/rozy">Розы</a></li>
                        <li><a href="/bukety">Букеты</a></li>
                        <li><a href="<?= Yii::app()->createUrl('/shop/category', ['id' => 86]) ?>">Корзины</a></li>
                   </ul>
                </div>
            </div>
        </div>
</footer>

</div>
<? ModuleHelper::getParam('counter'); ?>
<?/*?>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(57527377, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/57527377" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-158514744-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-158514744-1');
</script><?*/?>
<div style="display: none;">
	<div id="popup_shipping" class="popup_fancybox">
		<div class="popup_fancybox_img">
			<?= ModuleHelper::getParam('away_area_map') ?>
		</div>
		<div class="popup_fancybox_text">
			<?= ModuleHelper::getParam('away_area_text') ?>
		</div>
		<div class="clear"></div>
	</div>

	<div id="quality_text" class="popup_fancybox">
		<div class="popup_fancybox_cont">
			<div class="popup_fancybox_title">
				<img style="height: 60px;" src="/themes/template_11/img/logo.svg" alt=""/>
			</div>
			<div>
				<?= ModuleHelper::getParam('quality_text') ?>
			</div>
		</div>
	</div>

	<div id="return_text" class="popup_fancybox">
		<div class="popup_fancybox_cont">
			<div class="popup_fancybox_title">
				<img src="/themes/template_11/img/russroza.png" alt=""/>
			</div>
			<div>
				<?= ModuleHelper::getParam('return_text') ?>
			</div>
		</div>
	</div>

	<div id="delivery_text" class="popup_fancybox">
		<div class="popup_fancybox_cont">
			<div class="popup_fancybox_title">
				<img style="height: 60px;"  src="/themes/template_11/img/logo.svg" alt=""/>
			</div>
			<div>
				<?//= ModuleHelper::getParam('delivery_text') ?>
				<p>
					<span>
					Больше не нужно ездить по всему городу в поисках идеального букета для своих близких  - мы привезем выбранный вами букет в любую часть Москвы, Санкт-Петербурга, Екатеринбурга. <br><br>
					При заказе от 3000 руб. доставим цветы бесплатно!*<br><br>
					* Стоимость доставки в отдельные районы уточняйте, пожалуйста, у менеджеров.
					</span>
				</p>
			</div>
		</div>
	</div>

	<div id="popup_text" class="popup_fancybox">
		<div class="popup_fancybox_cont">
			<div class="popup_fancybox_title">
				<img src="/themes/template_11/img/russroza.png" alt=""/>
			</div>
			<p>Давно выяснено, что при оценке дизайна и композиции читаемый текстртное заполнение шаблона.</p>
		</div>
	</div>
</div>

<div class="preload-img">
	<img src="/images/menu_close.svg" alt="">
</div>

<script>
	$(function() {
		$('.fancybox').fancybox();
	})
</script>
</body>
</html>
