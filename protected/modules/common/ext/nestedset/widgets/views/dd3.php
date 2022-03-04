<?php
/** @var $this [\common\widgets\nestable\BaseNestable] */
/** @var $drugLabel string метка для элемента перетаскивания. По умолчанию тройной неразрывный пробел (&nbsp;). */
?>
<div class="cf nestable-lists">
	<?= CHtml::openTag('div', $this->htmlOptions); ?>
	<? if(!$this->dataProvider->getTotalItemCount()): ?>
		<div class="dd-empty">
			<?= $this->emptyText; ?>
		</div>
	<? else:
		$level=0;
		foreach($this->dataProvider->getData() as $data) {
			$itemLevel=$this->getItemLevel($data);
			if($itemLevel==$level)
				echo CHtml::closeTag('li')."\n";
			else if($itemLevel > $level)
				echo CHtml::openTag('ol', ['class'=>'dd-list'])."\n";
			else {
				echo CHtml::closeTag('li')."\n";
				for($i=($level-$itemLevel); $i; $i--) {
					echo CHtml::closeTag('ol')."\n";
					echo CHtml::closeTag('li')."\n";
				}
			}
		
			echo CHtml::openTag('li', ['class'=>'dd-item dd3-item', 'data-id'=>$this->getItemId($data)]);
			echo '<div class="dd-handle dd3-handle">'
				. (empty($drugLabel) ? '&nbsp;&nbsp;&nbsp;' : $drugLabel)
				. '</div><div class="dd3-content">' 
				. $this->getItemContent($data) 
				. '</div>';
			
			$level=$itemLevel;
		}
		for($i=$level; $i; $i--) {
			echo CHtml::closeTag('li')."\n";
			echo CHtml::closeTag('ol')."\n";
		}
	endif;
	?>
	<?= CHtml::closeTag('div'); ?>
</div>