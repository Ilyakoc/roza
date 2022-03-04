<?/*?><div class="news-block">
	<a href="/news" class="news-block__title">
		Новости
	</a>

	<div class="news-block-content">
		<?php foreach($events as $event): ?>
			<div class="news-block-content-item">
				<?php echo CHtml::link(CHtml::tag('h6', [], $event->title), array('site/event', 'id'=>$event->id), ['class' => 'news-block-content-item__link']); ?>
				<div class="news-block-content-item-inner">
					<p><?php echo $event->date; ?></p>
					<?php echo CHtml::link('Подробнее', array('site/event', 'id'=>$event->id)); ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?*/?>