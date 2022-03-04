<?php
/** @var common\ext\file\widgets\UploadFile $this */
/** @var boolean $labelDisable не отображать наименование атрибута. По умолчанию (FALSE) отображать. */
$labelDisable=true;
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<?= $form->labelEx($model, $b->attribute); ?>
	</div>
	<div class="panel-body">
		<? $this->render('upload_file', compact('b', 'form', 'model', 'labelDisable')); ?>
	</div>
</div>