<?
/** @var \common\widgets\form\TextAreaField $this */

echo $this->openTag();
echo $this->labelTag();

echo $this->form->textArea($this->model, $this->attribute, $this->htmlOptions);

echo $this->errorTag();
echo $this->closeTag();