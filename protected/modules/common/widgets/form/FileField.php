<?php
namespace common\widgets\form;

class FileField extends \CWidget
{
	public $form;
	public $model;
	public $attribute;
	
	public $htmlOptions=['class'=>'btn btn-primary'];

	public $note=false;
	
	public function run()
	{
		$this->render('file-field');
	}
}