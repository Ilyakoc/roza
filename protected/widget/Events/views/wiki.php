<?php
function getWikiIntro($string, $length = 350, $postfix = '...') {
    if (strlen($string) <= $length) {
        return $string;
    }

    $string = strip_tags($string);
    $string = substr($string, 0, $length);
    $string = rtrim($string, "!,.-");
    $string = substr($string, 0, strrpos($string, ' '));
    return $string . $postfix;
}
?>
<?/*?>
<div class="encyclopedia">
	<a href="/wiki" class="encyclopedia__title">Энциклопедия</a>

	<div class="encyclopedia-content">
		<?php foreach($events as $event): ?>
			<div class="encyclopedia-content-item">
				<a href="<?= Yii::app()->createUrl('/site/wiki', ['id' => $event->id]) ?>">
					<img src="<?= $event->oneImage ?>" alt="">
				</a>
				<?php echo CHtml::link(CHtml::tag('h6', [], $event->title), array('/site/wiki', 'id'=>$event->id), ['class' => 'encyclopedia-content-item__link']); ?>
				<p><?php echo getWikiIntro($event->intro); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?*/?>