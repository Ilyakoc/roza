<?php
/** @var BreadcrumbsArrayWidget $this */
/** @var array $breadcrumbs */
$arrayMenuNotActive = array('/bukety','/bukety-s-kustovymi-rozami','/shljapnye-korobki','/cvetochnye-korziny','/komu','/prazdniki');

foreach($breadcrumbs as $breadcrumb) {
	foreach($arrayMenuNotActive as $val) {
		if($breadcrumb["url"] == $val){
			$activeMenuAdd = $val;
		}
	}
}
if($this->importStyles) include('_styles.php');
?>
<script>
window.activeMenuAdd = activeMenuAdd = (<?echo json_encode($activeMenuAdd);?>);

if(window.activeMenuAdd != false){
	checkMenu();
}
function checkMenu() {
    $('.shop-menu li').each(function (i) {
        var elementMenu = this;
		elementMenuHref = $(this).find('a').attr('href');
		if(window.activeMenuAdd == elementMenuHref ){
			$(this).addClass('active');
		}
    });
}
</script>
<div class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList" name="breadcrumbs">
	<div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
		<a href="<?=Yii::app()->createUrl('site/index') ?>" itemprop="item"><span itemprop="name"><?=$this->homeTitle?></span><meta itemprop="position" content="1"></a>
	</div>
	<?php $i = 2; foreach($breadcrumbs as $item):?>
		<div>/</div>
			<?php if($item === end($breadcrumbs)):?>
            <div>
				<span><?= $item['title'] ?></span>
            </div>
			<?php elseif(isset($item['url'])):?>
            <div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
				<a href="<?= $item['url'] ?>" itemprop="item"><span itemprop="name"><?= $item['title'] ?></span><meta itemprop="position" content="<?= $i++ ?>"></a>
            </div>
			<?php else:?>
            <div>
				<span><?= $item['title'] ?></span>
            </div>
			<?php endif;?>
	<?php endforeach; ?>
</div>
