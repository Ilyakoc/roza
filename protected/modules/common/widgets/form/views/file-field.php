<div class="row">
    <?= $this->form->labelEx($this->model, $this->attribute); ?>
    <?if ($this->note) echo CHtml::tag('p', ['class'=>'note'], $this->note); ?>
    <?= $this->form->fileField($this->model, $this->attribute, $this->htmlOptions); ?>
    <?= $this->form->error($this->model, $this->attribute); ?>
</div>
