<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'blog-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ),
    )); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'title'); ?>
        <?php echo $form->textField($model, 'title'); ?>
        <?php echo $form->error($model, 'title'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'alias'); ?>
        <?php echo $form->textField($model, 'alias'); ?>
        <?php echo $form->error($model, 'alias'); ?>
    </div>

    <div class="row buttons">
        <div class="left">
		    <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'default-button')); ?>
            <?php echo CHtml::link('Отмена', array('default/index')); ?>
        </div>
        <?php if (!$model->isNewRecord): ?>
        <div class="right with-default-button">
            <a href="<?php echo $this->createUrl('delete', array('id'=>$model->id)); ?>"
               onclick="return confirm('Вы действительно хотите удалить блог?')">Удалить блог</a>
        </div>
        <?php endif; ?>
        <div class="clr"></div>
	</div>

    <?php $this->endWidget(); ?>
</div><!-- form -->
