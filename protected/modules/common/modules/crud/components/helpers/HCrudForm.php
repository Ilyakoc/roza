<?php
/**
 * Класс-помощник для форм модуля "CRUD"
 */
namespace crud\components\helpers;

use common\components\helpers\HArray as A;
use common\components\exceptions\ParamException;
use crud\components\helpers\HCrud;
use common\components\exceptions\ConfigException;

class HCrudForm
{
	/**
	 * Получить параметры для виджета \CActiveForm
	 * @param string $cid идентифкатор настроек CRUD для модели.
	 * @param string $pagePath путь к настройкам текущей страницы в конфигурации CRUD для текущей модели.
	 * @param array $properties дополнительные параметры формы.
	 * @return array
	 */
	public static function getFormProperties($cid, $pagePath, $properties=[])
	{
		$properties=A::m(
			HCrud::param($cid, 'crud.form', [], true), 
			A::m(
				HCrud::param($cid, $pagePath.'.form', [], true),
				$properties
			)
		);
        
        if(isset($properties['attributes'])) {
            unset($properties['attributes']);
        }
        if(isset($properties['buttons'])) {
            unset($properties['buttons']);
        }
                                       
        return $properties;
	}
	
	/**
	 * Получить HTML код полей формы
	 * @param string $cid идентифкатор настроек CRUD для модели.
	 * @param array $attributes массив конфигурации полей(атрибутов)
	 * @param \CActiveRecord|NULL $model объект модели. 
	 * По умолчанию (NULL) будет получена из настроек. 
	 * @param \CActiveForm|NULL $form объект формы. 
	 * По умолчанию (NULL) будет создан новый объект \CActiveForm. 
	 * @param \CController|NULL $controller объект контроллера.
	 * По умолчанию (NULL) будет получен из \Yii::app()->getController() 
	 */
	public static function getHtmlFields($cid, $attributes=[], $model=null, $form=null, $controller=null)
	{
		if(!is_array($attributes)) ParamException::e();
		
		if(!$controller) $controller=\Yii::app()->getController();
				
		if(array_key_exists('attributes.sort', $attributes)) {
			$attributesSort=A::get($attributes, 'attributes.sort', []);
			unset($attributes['attributes.sort']);
			$attributes=A::sort(
				$attributes, 
				$attributesSort, 
				!A::get($attributes, 'attributes.sort.filter', false),
				A::get($attributes, 'attributes.sort.reverse', false)
			);
		}
		if(array_key_exists('attributes.sort.filter', $attributes)) {
			unset($attributes['attributes.sort.filter']);
		}
		if(array_key_exists('attributes.sort.reverse', $attributes)) {
			unset($attributes['attributes.sort.reverse']);
		}
		
		$html='';
		foreach($attributes as $attribute=>$config)
		{
			$type=null;			
			if(!is_string($attribute)) {
				if(!is_string($config)) ConfigException::e();
				
				$attribute=$config;
				$type='text';
			}
			else {
				if(is_string($config)) $type=$config;
				elseif(is_array($config)) {
					if($phpCode=A::get($config, 'php')) {
						$html.=htmlspecialchars($phpCode);
					}
					elseif(!($type=A::get($config, 'type'))) {
						ConfigException::e();
					}
				}
				else {
					ConfigException::e();
				}
			}
			
			if(is_string($type)) {
				switch(strtolower($type)) {
					case 'common.ext.file.image':
						$behaviorName=A::get($config, 'behaviorName', 'imageBehavior');
						$params=A::m([
							'actionDelete'=>'/common/crud/admin/default/removeImage?cid='.$cid.'&id='.$model->id.'&b='.$behaviorName,
							'tmbWidth'=>200,
							'tmbHeight'=>200,
							'view'=>'panel_upload_image'
						], A::get($config, 'params', []));
						$params['form']=$form;
						$params['behavior']=$model->asa($behaviorName);
						$html.=$controller->widget('\common\ext\file\widgets\UploadFile', $params, true);
						break;
						
					case 'common.ext.data':
						if($behaviorName=A::get($config, 'behaviorName')) {
							$params=[
								'form'=>$form,
								'model'=>$model,
								'attribute'=>$attribute,
								'behavior'=>$model->asa($behaviorName),
								'params'=>A::get($config, 'params', [])
							];
							$params=A::m($params, A::get($config, 'extendParams', []));
							$html.=$controller->widget('\common\widgets\form\ExtDataAttributeField', $params, true);
						}
						break;
						
					default:
						$typeClass='\common\widgets\form\\'.ucfirst($type).'Field';
						$params=A::get($config, 'params', []);
						if(!A::get($config, 'strictParams', false)) {
							$params['form']=$form;
							$params['model']=$model;
							$params['attribute']=$attribute;
						}
						$html.=$controller->widget($typeClass, $params, true);
				}
			}
		}
		
		return $html;
	}
} 