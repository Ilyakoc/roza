<?php
namespace common\ext\file\widgets;

use common\components\base\Widget;

class UploadFile extends Widget
{
	/** 
	 * @var \common\ext\file\behaviors\FileBehavior объект поведения файла
	 */ 
	public $behavior;
	
	/**
	 * @var \CActiveForm объект формы
	 */
	public $form;
	
	/**
	 * @var \CAction действие удаления
	 */
	public $actionDelete;
	
	/**s
	 * @var integer ширина превью-изображения для режима $behavior::$imageMode=TRUE.
	 */
	public $tmbWidth=150;
	
	/**
	 * @var integer высота превью-изображения для режима $behavior::$imageMode=TRUE.
	 */
	public $tmbHeight=150;
	
	/**
	 * @var boolean пропорциональное преобразование изображения в превью.
	 * По умолчанию (TRUE) - пропорциональное.
	 */
	public $tmbProportional=true;
	
	/**
	 * @var string|NULL имя шаблона отображения. По умолчанию (NULL) 
	 * будут использованы стандартные "upload_file" и "upload_image"
	 * для режима $imageMode=TRUE соответственно.
	 */
	public $view=null;
	
	/**
	 * (non-PHPdoc)
	 * @see \common\components\base\Widget::$tagOptions
	 */
	public $tagOptions=['class'=>'panel panel-default'];

	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		if($this->view) $view=$this->view;
		else {
			$view=$this->behavior->imageMode ? 'upload_image' : 'upload_file';
		}

		$this->render($view, [
			'b'=>$this->behavior, 
			'form'=>$this->form, 
			'model'=>$this->behavior->owner
		]);
	}
}