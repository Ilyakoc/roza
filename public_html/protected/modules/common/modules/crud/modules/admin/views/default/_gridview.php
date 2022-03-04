<?php
/** @var \CController $this  */
/** @var string $cid индетификатор настроек CRUD для модели. */
/** @var \CActiveDataProvider $dataProvider */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHash;
use crud\components\helpers\HCrud;
use crud\components\helpers\HCrudView;
use common\components\helpers\HRequest;

$t=Y::ct('\crud\modules\admin\AdminModule.controllers/default', 'common.crud');

$gridViewConfig=HCrud::param($cid, 'crud.index.gridView', []);
$gridViewConfig['id']=HCrudView::getGridId($cid);

unset($gridViewConfig['dataProvider']);

if($sortable=HCrud::getSortable($cid, 'crud.index.gridView')) {
	unset($gridViewConfig['sortable']);
}

HCrudView::prepareGridView($cid, $gridViewConfig);

if($sortable && !A::get($sortable, 'disabled', false)) {
	$sortableUrl=HCrud::getConfigUrl($cid, 'crud.index.gridView.sortable.url', '/crud/admin/default/sortableSave', ['cid'=>$cid]);
	$sortableWidget=$this->widget('\common\ext\sort\widgets\Sortable', [
		'id'=>'sort'.$gridViewConfig['id'],
		'initialize'=>false,
		'options'=>[
			'category'=>A::get($sortable, 'category'),
			'key'=>A::get($sortable, 'key'),
			'saveUrl'=>$this->createUrl($sortableUrl[0], $sortableUrl[1]),
			'selector'=>A::get($sortable, 'selector', '#'.$gridViewConfig['id'].' > table > tbody'),
			'dataId'=>A::get($sortable, 'dataId', 'id'),
			'autosave'=>A::get($sortable, 'autosave', false),
			'onAfterSave'=>A::get($sortable, 'onAfterSave', null),
		]
	]);

	$sortableSortedDesc=is_numeric(HRequest::requestGet('usortd')) ? ((int)HRequest::requestGet('usortd')?1:0) : null;
	$sortableSortedDescCssClass=Y::requestGet($dataProvider->sort->sortVar) ? '' : (($sortableSortedDesc===null)?'asc':($sortableSortedDesc?'desc':'asc'));
	$gridViewConfig['summaryText']=A::get($gridViewConfig, 'summaryText', $t('sortable.summaryText'))
		. '<div class="btn-group pull-left" data-toggle="buttons">' 
		. CHtml::ajaxLink($t('sortable.btnUserSort'), 'javascript:;', 
			['beforeSend'=>'js:function(){'
				. '$("#'.$gridViewConfig['id'].'").yiiGridView("update",{data:{s:"",usort:1,usortd:+$("#sortbtnsort_'.$gridViewConfig['id'].'").data("usortd")}});'
				. 'return false;}'
			], 
			['id'=>'sortbtnsort_'.$gridViewConfig['id'], 
				'class'=>'btn btn-xs btn-primary crud-sort-link '.$sortableSortedDescCssClass, 
				'title'=>$t('sortable.btnUserSort.help'), 
				'data-usortd'=>($sortableSortedDesc===null)?'0':($sortableSortedDesc?0:1)
			])
		. CHtml::ajaxButton($t('sortable.btnInit'), 'javascript:;', 
			['beforeSend'=>'js:function(){var $btn=$("#sortbtnact_'.$gridViewConfig['id'].'");'
				. '$btn.removeClass("btn-danger");$btn.addClass("btn-success");'
				. '$("#sortbtnsave_'.$gridViewConfig['id'].'").removeAttr("disabled");'
				. $sortableWidget->getJsInit().'return false;}'
			], 
			['id'=>'sortbtnact_'.$gridViewConfig['id'], 
				'class'=>'btn btn-xs btn-danger', 
				'disabled'=>$sortableSortedDesc || (bool)HRequest::requestGet($dataProvider->pagination->pageVar)
			])
		. CHtml::ajaxButton($t('sortable.btnSave'), 'javascript:;',
			['beforeSend'=>'js:function(){var $btn=$("#sortbtnsave_'.$gridViewConfig['id'].'");'
				. $sortableWidget->getJsSave()
				.'$btn.removeClass("btn-primary");$btn.addClass("btn-success");'
				. 'setTimeout(function(){$btn.removeClass("btn-success");$btn.addClass("btn-primary");}, 700);'
				. 'return false;}'
			],
			['id'=>'sortbtnsave_'.$gridViewConfig['id'], 'class'=>'btn btn-xs btn-primary', 'disabled'=>true])
		. '</div>';
}

$gridViewConfig['itemsCssClass']='table table-striped  table-bordered table-hover items_sorter';
$gridViewConfig['rowHtmlOptionsExpression']='["id"=>$data->id]';
$gridViewConfig['enableHistory']=true;
$gridViewConfig['emptyText']=A::get($gridViewConfig, 'emptyText', $t('list.emptyText'));
$gridViewConfig['dataProvider']=$dataProvider;
$gridViewConfig['loadingCssClass']=A::get($gridViewConfig, 'loadingCssClass', 'crud_grid-view_loader');

$gridViewConfig['pagerCssClass']=A::get($gridViewConfig, 'pagerCssClass', 'clearfix');
$gridViewConfig['pager']=A::m([
	'class'=>'\CLinkPager',
	'header'=>CHtml::tag('ul', ['class'=>'pull-left pagination pagination-sm'], array_reduce([30,60,120], function($html, $size) use ($cid, $dataProvider) {
		$htmlOptions=($dataProvider->pagination->pageSize == $size) ? ['class'=>'active'] : [];
		return $html.=CHtml::tag(
			'li', 
			$htmlOptions, 
			CHtml::link(
				$size, 
				HCrud::getConfigUrl($cid, 'crud.index.url', $this->action->id, ['cid'=>$cid, 'gridSize'=>$size], 'c')
			)
		);
	})),
	'firstPageLabel'=>'&lArr;',
	'prevPageLabel'=>'&laquo;',
	'nextPageLabel'=>'&raquo;',
	'lastPageLabel'=>'&rArr;',
	'htmlOptions'=>['class'=>'pagination pagination-sm pull-right']
], A::get($gridViewConfig, 'pager', []));

$this->widget('zii.widgets.grid.CGridView', $gridViewConfig);
?>