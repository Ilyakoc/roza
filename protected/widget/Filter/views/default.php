<?php
/**/$id=Yii::app()->request->getQuery('id');
$useid=false;
if($id && HYii::isAction($this->owner, 'shop', 'category')) { $url='shop/category'; $useid=true;}
elseif($id && HYii::isAction($this->owner, 'shop', 'product')) { 
	$url='shop/category'; 
	if($model=Product::model()->findByPk($id, ['select'=>'category_id'])) {
		$id=$model->category_id;
		$useid=true; 
	}
	else {
		$url='shop/index';
		$id=null;
		$useid=false; 
	}
}
else $url='shop/index';

//$url='shop/index';
?>
<div class="filter-head">
	<div class="menu-title">
		<span>Доставляем цветы по Мск, СПб, Екб</span>
	</div>
	<div class="filter-menu-tuggle-batton">
		<div class="filter-menu-tuggle-icon"></div>
		<div class="filter-menu-tuggle-icon"></div>
		<div class="filter-menu-tuggle-icon"></div>
	</div>
</div>
<div class="filter-list"><?
foreach([[0,5000],[5000,10000],[10000,15000],[15000,20000],[20000,0]] as $range):
	list($min,$max)=$range;
	$title=($max?'':'от ').($min?:'до ').($min&&$max?' - ':'').($max?:'').' руб.';
	$params=[];
	if($useid && $id) $params['id']=$id;
	if($min) $params['min']=$min;
	if($max) $params['max']=$max;
	$_url=Yii::app()->createUrl($url, $params);
	if($_url == $_SERVER['REQUEST_URI']) $active.=' active';
	else $active='';
	echo CHtml::link($title, $_url, ['class' => 'tags-for-price'.$active]);
endforeach;
?>
</div>

