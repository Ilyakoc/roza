<?php
/**
 * Класс-помощник для шаблонов отображения модуля "CRUD"
 */
namespace crud\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHtml;
use common\components\exceptions\ConfigException;
use crud\components\helpers\HCrud;
use common\components\helpers\HHash;

class HCrudView
{
	/**
	 * Получить идетификатор для \CGridView
     * @param string $cid идентифкатор настроек CRUD для модели.
	 * @param string $pagePath путь к настройкам текущей страницы в конфигурации CRUD для текущей модели.
	 * По умолчанию "crud.index".
	 */
	public static function getGridId($cid, $pagePath='crud.index')
	{
		return HCrud::param($cid, $pagePath.'.gridView.id', 'grid'.HHash::ucrc32(HCrud::param($cid, 'class')));		
	}
	
	/**
	 * Получить значение параметра "tabs" для виджета zii.widgets.jui.CJuiTabs
	 * @param string $cid идентифкатор настроек CRUD для модели.
	 * @param string $pagePath путь к настройкам текущей страницы в конфигурации CRUD для текущей модели.
	 * @param array $properties дополнительные параметры для параметра "tabs" (в формате параметров виджета).
	 * @param array $options дополнительные параметры для генерации вкладки. 
	 * Может содержать следующие параметры:
	 * "controller" - контроллер, для которого будет вызван renderPartial для генерации контента вкладки.
	 * По умолчанию \Yii::app()->getController();
	 * 
	 * "model" - объект модели, который будет передан в шаблон отображения вкладки.
	 * По умолчанию NULL.
	 * Может быть передано TRUE, в этом случае, будет создана автоматически новый объект модели;
	 * Может быть передан (string|integer) id (идентификатор модели) - будет произведена попытка 
	 * получить модель по идентификатору;
	 * Может быть передан массив [className] имя класса, будет создана модель данного класса; 
	 * Может быть передан массив [className, id] будет произведена попытка получить модель 
	 * класса className с идентификатором id;
	 * 
	 * "form" - объект формы \CActiveForm. По умолчанию будет создан новый объект \CActiveForm;
	 * 
	 * "formView" - шаблон отображения вкладки по умолчанию. По умолчанию 
	 * "crud.modules.admin.views.default._tabs_form".
	 * @return array
	 */
	public static function getTabs($cid, $pagePath, $properties=[], $options=[])
	{
		$tabs=[];
		
		$tabsConfig=A::m(
			$a1=HCrud::param($cid, 'crud.tabs', [], true),
			$a2=HCrud::param($cid, $pagePath.'.tabs', [], true)
		);
		if(!empty($tabsConfig)) {
			foreach($tabsConfig as $idx=>$config) {
				$tabConfig=HCrud::param($cid, $config, [], true);
				if(!A::get($tabConfig, 'disabled', false)) {
					if($title=trim(A::get($tabConfig, 'title', $idx))) {
						$tabId=A::get($tabConfig, 'id', 'tab-'.$idx);
						if($ajax=A::get($tabConfig, 'ajax')) {
							$tabs[$title]=['id'=>$tabId, 'ajax'=>$ajax];
						}
						else {
							$formView=A::get($options, 'formView', 'crud.modules.admin.views.default._tabs_form');
							if($view=A::get($tabConfig, 'view', $formView)) {
								$controller=A::get($options, 'controller', \Yii::app()->getController());
								
								if(!($form=A::get($options, 'form'))) $form=new \CActiveForm();
															
								$model=A::get($options, 'model');
								if($model === true) $model=HCrud::getById($cid);
								elseif(is_array($model)) {
									if(!$className=A::get($model, 0)) {
										ConfigException::e();
									}
									if($pk=A::get($model, 1)) {
										$model=$className::model()->findByPk($pk);
									}
									else {
										$model=new $className();
									}
								}
								elseif(is_string($model) || is_integer($model)) {							
									$model=HCrud::getById($cid, $model);
								}
								
								$attributes=A::get($tabConfig, 'attributes', []);
								
								$render=true;
								if($onBeforeRender=A::get($tabConfig, 'onBeforeRender')) {
									$render=call_user_func_array($onBeforeRender, compact('cid', 'form', 'model', 'attributes'));
								}
								if($render) {
									$content=$controller->renderPartial(
										$view,
										compact('cid', 'form', 'model', 'attributes'), 
										true,
										A::get($tabConfig, 'processOutput', false)
									);
								
									$tabs[$title]=['id'=>$tabId, 'content'=>$content];
								}
							}
						}
					}	
				}
			}
		}
		
		return A::m($tabs, $properties); 
	}
	
	/**
	 * Подготовить конфигурацию для \CGridView.
	 * @param string $cid идентифкатор настроек CRUD для модели.
	 * @param &array $gridViewConfig массив параметров \CGridView
	 */
	public static function prepareGridView($cid, &$gridViewConfig)
	{
		if(!isset($gridViewConfig['columns'])) return;

		$tbtn=Y::ct('CommonModule.btn', 'common');
		$tlbl=Y::ct('CommonModule.labels', 'common');
		
		$urlUpdate=HCrud::getConfigUrl(
			$cid,
			'crud.update.url', 
			'/common/crud/admin/default/update', 
			['cid'=>$cid, 'id'=>'php:$data->id'],
			's'
		);
		$urlDelete=HCrud::getConfigUrl(
			$cid,
			'crud.delete.url', 
			'/common/crud/admin/default/delete', 
			['cid'=>$cid, 'id'=>'php:$data->id'],
			's'
		);
				
		$buttons=[
			'class'=>'\CButtonColumn',
			'template'=>'{update}{delete}',
			'updateButtonImageUrl'=>false,
			'deleteButtonImageUrl'=>false,
			'buttons'=>[
				'update'=>[
					'label'=>'<span class="glyphicon glyphicon-pencil"></span> ',
					'url'=>'\Yii::app()->createUrl("'.$urlUpdate[0].'", '.$urlUpdate[1].')',
					'options'=>['title'=>$tbtn('edit')],
				],
				'delete' => [
					'label'=>'<span class="glyphicon glyphicon-remove"></span> ',
					'url'=>'\Yii::app()->createUrl("'.$urlDelete[0].'", '.$urlDelete[1].')',
					'options'=>['title'=>$tbtn('remove')],
				]
			]
		];
		
		foreach($gridViewConfig['columns'] as $idx=>$column) {
			if($column=='crud.buttons') {
				$gridViewConfig['columns'][$idx]=$buttons;
			}
			elseif(is_array($column)) {
				if($type=A::get($column, 'type')) {
					if(is_array($type)) {
						$typeKey=key($type);
						$typeParams=$type[$typeKey];
					}
					else {
						$typeKey=$type;
						$typeParams=[];
					}
					
					switch($typeKey) {
						case 'crud.buttons':
							$gridViewConfig['columns'][$idx]=A::m($buttons, A::get($column, 'params', []));
							break;
							
						case 'common.ext.active':
							$column['type']='raw';
							$behaviorName=A::get($typeParams, 'behaviorName', 'activeBehavior');
							$columnOptions=[
        						'headerHtmlOptions'=>['style'=>'width:10%'],
        						'htmlOptions'=>['style'=>'text-align:center'],
        						'value'=>'$this->grid->owner->widget("\common\\\\ext\active\widgets\InList", [
        							"behavior"=>$data->asa("'.$behaviorName.'"),
        							"changeUrl"=>$this->grid->owner->createUrl("/common/crud/admin/default/changeActive", ["cid"=>"'.$cid.'", "id"=>$data->id, "b"=>"'.$behaviorName.'"]),
					        		"cssMark"=>"unmarked",
					        		"cssUnmark"=>"marked",
					        		"wrapperOptions"=>["class"=>"mark"]
        						], true)'
         					];
							$gridViewConfig['columns'][$idx]=A::m($columnOptions, $column);      
							break;
							
						case 'common.ext.file.image':
							$column['type']='raw';
							$behaviorName=A::get($typeParams, 'behaviorName', 'imageBehavior');
							$width=A::get($typeParams, 'width', 120);
							$height=A::get($typeParams, 'height', 120);
							$proportional=A::get($typeParams, 'proportional', true)?'true':'false';
							$htmlOptions=A::toPHPString(A::get($typeParams, 'htmlOptions', []));
							$default=A::get($typeParams, 'default', true);
							if($default === true) {
								$default=\CHtml::image(HHtml::pImage(['w'=>$width, 'h'=>$height, 't'=>$tlbl('nophoto'), 'sz'=>12]));
							}
							elseif($default === false) {
								$default='&nbsp;';
							}
							else {
								$default=\CHtml::image($default);
							}
							$columnOptions=[
        						'headerHtmlOptions'=>['style'=>'width:15%'],
        						'htmlOptions'=>['style'=>'text-align:center'],
        						'value'=>'$data->'.$behaviorName.'->img('.$width.','.$height.','.$proportional.','.$htmlOptions.')?:"'.HHtml::q($default).'"' 
         					];
							$gridViewConfig['columns'][$idx]=A::m($columnOptions, $column);      
							break;
					}
				}
			}
		}
		
		if(array_key_exists('columns.sort', $gridViewConfig)) {
			if($columnsSort=A::get($gridViewConfig, 'columns.sort')) {
				$gridViewConfig['columns']=A::sort(
					$gridViewConfig['columns'], 
					$columnsSort, 
					!A::get($gridViewConfig, 'columns.sort.filter', false),
					A::get($gridViewConfig, 'columns.sort.reverse', false)
				);
			}
			unset($gridViewConfig['columns.sort']);
		}		
		if(array_key_exists('columns.sort.filter', $gridViewConfig)) {
			unset($gridViewConfig['columns.sort.filter']);			
		}		
		if(array_key_exists('columns.sort.reverse', $gridViewConfig)) {
			unset($gridViewConfig['columns.sort.reverse']);			
		}
	}
}
