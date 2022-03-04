<h1>Результаты поиска "<?=$_GET['q']?>"</h1>
<? /* ?>
<?php if($eventsDataProvider->getTotalItemCount()): ?>
	<h2>Новости</h2>
	
	<?php $this->widget('zii.widgets.CListView', array(
		'dataProvider'=>$eventsDataProvider,
		'itemView'=>'event_item',
		'itemsTagName'=>'ol',
		'pagerCssClass' => 'pager search-pager',
		'pager' => array(
			'header'=>'Страницы: ',
			'nextPageLabel'=>'&gt;',
			'prevPageLabel'=>'&lt;',
			'cssFile'=>false,
			'htmlOptions'=>array('class'=>'news-pager')
		)
	)); ?>
<?php endif; ?>

<?php if($pagesDataProvider->getTotalItemCount()): ?>
	<h2>Страницы</h2>
	<?php $this->widget('zii.widgets.CListView', array(
		'dataProvider'=>$pagesDataProvider,
		'itemView'=>'page_item',
		'itemsTagName'=>'ol',
		'pagerCssClass' => 'pager search-pager',
		'pager' => array(
	        'header'=>'Страницы: ',
	        'nextPageLabel'=>'&gt;',
	        'prevPageLabel'=>'&lt;',
	        'cssFile'=>false,
	        'htmlOptions'=>array('class'=>'news-pager')
	    )
	)); ?>
<?php endif; ?>
<? /**/ ?>
<?php if($data_p->getTotalItemCount()): ?>
	<?php 
	$products = $data_p->getData(); 
	$pages = $data_p->getPagination();
	?>

	<?php $this->renderPartial('//shop/_category_js'); ?>
	
	<div id="product-list-module">
    <?php $this->renderPartial('//shop/_products', compact('products', 'pages')); ?>	
	</div>
<?php endif; ?>

<?php if(!$data_p->getTotalItemCount() /* && !$pagesDataProvider->getTotalItemCount() && !$eventsDataProvider->getTotalItemCount()*/): ?>
	<?php echo '<br /><i>Не найдено</i>'; ?>
<?php endif; ?>
