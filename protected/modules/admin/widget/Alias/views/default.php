<?php
/** @var $this AliasFieldWidget */

echo $this->form->labelEx($this->model, $this->attributeAlias); 
echo $this->form->textField($this->model, $this->attributeAlias, array(
	'size'=>160,
    'maxlength'=>255, 
    'class'=>'form-control inline',
));
if(!$this->model->isNewRecord) { 
	echo '&nbsp;'.CHtml::button('Обновить', array(
    	'class'=>'btn default-button js-afw-btn-update'
  	));
}
echo $this->form->error($this->model, $this->attributeAlias); 
?>