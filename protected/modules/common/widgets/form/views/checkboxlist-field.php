<?
/** @var \common\widgets\form\CheckboxField $this */

echo $this->openTag();

echo $this->labelTag();
echo $this->form->checkBoxList($this->model, $this->attribute, $this->data, $this->htmlOptions);
echo $this->errorTag();

echo $this->closeTag();