<?
/** @var \common\widgets\form\AliasField $this */

echo $this->openTag();
echo $this->labelTag();

echo $this->form->textField($this->model, $this->attribute, $this->htmlOptions);

if(!$this->model->isNewRecord && $this->btnUpdate) {
	echo '&nbsp;' . \CHtml::button($this->btnLabel, $this->btnOptions);
}

echo $this->errorTag();
echo $this->closeTag();