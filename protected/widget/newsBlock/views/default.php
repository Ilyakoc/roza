<div class="newsBlock">
	<div class="three-col-main__col pull-left">
		<h2><div class="dib">Новости (<a href="/news"><?php echo $countNews ?></a>)</div></h2>
		<?php echo CHtml::image($oneNew->oneImage); ?>
		<div class="nn-date"><?php echo $oneNew->date; ?></div>
		<div class="nn-header">
			<?php echo CHtml::link($oneNew->title, array('site/event', 'id'=>$oneNew->id)); ?>
		</div>
		<p><?php echo $oneNew->intro; ?></p>
	</div>

	<div class="three-col-main__col pull-left">
		<h2><div class="dib">Статьи (<a href="/articles"><?php echo $countArticle ?></a>)</div></h2>
		<?php echo CHtml::image($oneArticle->oneImage); ?>
		<div class="nn-date"><?php echo $oneArticle->date; ?></div>
		<div class="nn-header">
			<?php echo CHtml::link($oneArticle->title, array('site/article', 'id'=>$oneArticle->id)); ?>
		</div>
		<p><?php echo strip_tags($oneArticle->intro); ?></p>
	</div>

	<div class="three-col-main__col pull-left">
		<h2><div class="dib">Энциклопедия (<a href="/wiki"><?php echo $countWiki ?></a>)</div></h2>
		<?php echo CHtml::image($oneWiki->oneImage); ?>
		<div class="nn-date"><?php echo $oneWiki->date; ?></div>
		<div class="nn-header">
			<?php echo CHtml::link($oneWiki->title, array('site/wiki', 'id'=>$oneWiki->id)); ?>
		</div>
		<p><?php echo $oneWiki->intro; ?></p>
	</div>
</div>
