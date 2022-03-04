<?php
/** @var NestableWidget $this */
/** @var array[CModel] $data */
YiiHelper::csjs(uniqid('nw'), 
"$('#{$this->id}').on('mousedown', function(e) {if($(e.target).is('a, a span')) { e.stopImmediatePropagation(); $(e.target).click(); return false;}});
NestableWidget.init('{$this->id}'".D::c($this->mode=='basic',',{maxDepth:1}').");", 
CClientScript::POS_READY);
?>
<div class="cf nestable-lists site_menu">
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
			echo CHtml::openTag('li', array('class'=>'dd-item'.($item['isSubmenuRoot']?' is_submenu_root':''), 'data-id'=>$item[$this->attributeId]));
			?><div class="dd-handle">
				<?=D::c($this->showId, ' ['.$item[$this->attributeId].'] ').CHtml::link(CHtml::encode($item[$this->attributeTitle]), $item['url'])?>
				<a title="Удалить" class="<?=$item['disabledClass']?>_button <?=D::c($item['urlDelete'],'delete-ajax')?> site_menu-delete delete pull-right" href="<?=$item['urlDelete']?>"><span class="glyphicon glyphicon-remove"></span></a>
				<a title="Редактировать" class="site_menu-edit pull-right" href="<?=$item['url']?>"><span class="glyphicon glyphicon-pencil"></span></a>
			</div><?
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