<?
/** @var \common\widgets\form\TinyMceField $this */
use common\components\helpers\HYii as Y;

$ta=Y::ct('AdminModule.admin');
 
echo $this->openTag();
echo $this->labelTag();

$this->widget('admin.widget.EditWidget.TinyMCE', [
	'editorSelector'=>uniqid('tinymce'),
	'model'=>$this->model,
	'attribute'=>$this->attribute,
	'htmlOptions'=>$this->htmlOptions,
	'full'=>$this->full
]);

echo $this->errorTag();
echo $this->closeTag();

if($this->showAccordion) {
	$this->widget('admin.widget.Accordion.AccordionList');
}

if( !$this->model->isNewRecord ) {
	if($this->uploadImages) { 
		$this->widget('admin.widget.ajaxUploader.ajaxUploader', [
	    	'fieldName'=>'images',
	    	'fieldLabel'=>$ta('label.uploadImages'),
	    	'model'=>$this->model,
	    	'fileType'=>'image'
	  	]);
	}
	
	if($this->uploadFiles) {
		$this->widget('admin.widget.ajaxUploader.ajaxUploader', [
    		'fieldName'=>'files',
    		'fieldLabel'=>$ta('label.uploadFiles'),
    		'model'=>$this->model,
  		]);
	}
}