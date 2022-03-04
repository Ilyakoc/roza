<?php
/**
 * NestedSet widget
 * Сделано для hasManyRoots=TRUE и необходимо дополнительное поле у модели "ordering".
 * Виджет редактирования вложенной структуры
 * @see https://github.com/dbushell/Nestable
 * @see https://github.com/yiiext/nested-set-behavior/blob/master/readme_ru.md#%D0%9F%D0%BE%D0%BB%D0%B5%D0%B7%D0%BD%D1%8B%D0%B9-%D0%BA%D0%BE%D0%B4
 */
class NestedSetWidget extends CWidget
{
	public $id;
	
	/**
	 * @var string имя класса модели с поведением NestedSetBehavior
	 * @see https://github.com/yiiext/nested-set-behavior
	 */
	public $model;
	
	/**
 	 * @var string имя атрибута id модели
	 */
	public $attributeId='id';
	
	/**
 	 * @var string имя атрибута заголовка
	 */
	public $attributeTitle='title';
	
	/**
	 * @var boolean использовать скин dd3
	 */
	public $skinDd3=false;
	
	/**
	 * @var string базовая ссылка (URL) модели 
	 */
	public $modelBaseUrl=false;
	 
	/**
	 * @var string текст ссылки (URL) модели 
	 */
	public $modelUrlText=false;
	
	/**
	 * @var array htmlOptions для тэга ссылки (URL) модели
	 */
	public $modelUrlOptions=['target'=>'_blank', 'style'=>'float:right'];
	 
	/**
	 * (non-PHPdoc)
	 * @see CWidget::init()
	 */
	public function init()
	{
		AssetHelper::publish(array(
			'path'=>dirname(__FILE__).DS.'assets',
			'js'=>array('jquery.nestable.js', 'NestableWidget.js'),
			'css'=>'jquery.nestable.css'
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		$modelClass=$this->model;
		$tableName=$modelClass::model()->tableName();
		
		$query="SELECT `d`.`{$this->attributeId}`,`d`.`{$this->attributeTitle}`,`d`.`root`,`d`.`level`,`d`.`ordering`
				FROM `{$tableName}` as `d` ORDER BY `d`.`root`,`d`.`lft`"; 
		
		$data=Yii::app()->db->createCommand($query)->queryAll();
		
		// сортировка корневых элементов
		if($data) {
			$roots=array();
			foreach($data as $item) { 
				if($item['id']==$item['root']) 
					$roots[$item['id']]=(int)$item['ordering'];
			}
			asort($roots, SORT_NUMERIC);
			$_data=array();
			foreach($roots as $id=>$ordering) {
				foreach($data as $idx=>$item) {
					if($item['root']==$id) {
						$_data[]=$item;
						unset($data[$idx]);
					}
				}
			}
			$data=$_data;
		}
		
		$this->render('default', compact('data'));
	}
}