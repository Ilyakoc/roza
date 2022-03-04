<?php
/** @var \common\widgets\ui\popover\Popover $this */

echo \CHtml::openTag('div', $this->htmlOptions);
?><div class="arrow"></div>
	<?php if($this->title): ?>
		<h3 class="popover-title"><?= $this->title; ?></h3>
	<?php endif; ?>
	<div class="popover-content">
    	<p><?= $this->content; ?></p>
	</div>
<?= \CHtml::closeTag('div'); ?>