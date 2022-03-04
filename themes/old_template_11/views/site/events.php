<?php $this->pageTitle = 'Новости - '. Yii::app()->name; ?>

    <h1>Новости</h1>
    <div class="events-page three-col-main list-page clearfix">
        <?php foreach($events as $event): ?>
		<div class="three-col-main__col">
			<?php echo CHtml::image($event->oneImage); ?>
			<div class="nn-date"><?php echo $event->date; ?></div>
			<div class="nn-header">
				<?php echo CHtml::link($event->title, array('site/event', 'id'=>$event->id)); ?>
			</div>
			<p><?php echo $event->intro; ?></p>
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
      



