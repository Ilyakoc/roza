<?php $this->pageTitle = 'Энциклопедия - '. Yii::app()->name; ?>


    <h1>Энциклопедия</h1>
    <div class="encyclopedia-content">
        <?php foreach($events as $event): ?>
		<div class="encyclopedia-content-item no-bd">
			<?php echo CHtml::image($event->oneImage); ?>
			<div class="encyclopedia-content-item__link ptt">
				<h6 class="no-pt"><?php echo CHtml::link($event->title, array('site/wiki', 'id'=>$event->id)); ?></h6>
			</div>
			<p class="is_read_more"><?php echo $event->intro; ?></p>
		</div>
        <?php endforeach; ?>
    </div>

    <?php $this->widget('CLinkPager', array(
        'header'=>'Страницы: ',
        'pages'=>$pages,
        'nextPageLabel'=>'&gt;',
        'prevPageLabel'=>'&lt;',
        'cssFile'=>false,
        'htmlOptions'=>array('class'=>'news-pager')
    )); ?>
      



