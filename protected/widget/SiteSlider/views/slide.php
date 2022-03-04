<div class="slider-top-wrapp">
	<ul class="slider-topj">
		<?php $i = 1; foreach($slides as $slide): ?>
			<a href="<?= $slide->link ?>">
				<?php if (!$slide->hide_title): ?>
					<div class="s-headerrr"><?php echo $slide->title; ?></div>
				<?php endif ?>
				<!-- <a class="chooosebooker" href="<?php echo $slide->link; ?>" target="blanc">Выбрать букет</a> -->
				<img src="<?php echo $slide->src; ?>" alt="" />
			</a>
		<?php $i++; endforeach; ?>
	</ul>
</div>
