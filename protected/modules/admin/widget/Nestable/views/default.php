<?php
/** @var NestableWidget $this */
/** @var array[CModel] $data */
use HYii as Y;
use HArray as A;

Y::js(false, "NestableWidget.init('{$this->id}');", CClientScript::POS_READY);

$this->modelUrlOptions['class']=trim(A::get($this->modelUrlOptions, 'class', '').' dd-nodrag');
?>
<div class="cf nestable-lists">
	<div class="dd" id="<?=$this->id?>">
<?if(!$data):?>
	Категорий не найдено.
<?else:
	$level=0;
	foreach($data as $n=>$item) {
		if($item['level']==$level)
			echo CHtml::closeTag('li')."\n";
		else if($item['level']>$level)
			echo CHtml::openTag('ol', array('class'=>'dd-list'))."\n";
		else {
			echo CHtml::closeTag('li')."\n";
			for($i=($level-$item['level']);$i;$i--) {
				echo CHtml::closeTag('ol')."\n";
				echo CHtml::closeTag('li')."\n";
			}
		}
	
		if($this->skinDd3) {
			echo CHtml::openTag('li', array('class'=>'dd-item dd3-item', 'data-id'=>$item[$this->attributeId]));
			echo '<div class="dd-handle dd3-handle">Drug</div><div class="dd3-content">'.CHtml::encode($item[$this->attributeTitle]).'</div>';
		}
		else {
			$url='';
			if($this->modelBaseUrl) {
				$url=\CHtml::link($this->modelUrlText, [$this->modelBaseUrl, 'id'=>$item[$this->attributeId]], $this->modelUrlOptions);
			}
			
			echo CHtml::openTag('li', array('class'=>'dd-item', 'data-id'=>$item[$this->attributeId]));
			
			
			echo '<div class="dd-handle">'.CHtml::encode($item[$this->attributeTitle]).$url.'</div>';
		}
		$level=$item['level'];
	}
	for($i=$level;$i;$i--) {
		echo CHtml::closeTag('li')."\n";
		echo CHtml::closeTag('ol')."\n";
	}
endif;
?>
	</div>
</div>