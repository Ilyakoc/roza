
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
    'id'=>'banner-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
    ),
    'htmlOptions' => array('enctype'=>'multipart/form-data'),
)); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'title'); ?>
        <?php echo $form->textField($model,'title'); ?>
        <?php echo $form->error($model,'title'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'link'); ?>
        <?php echo $form->textField($model,'link'); ?>
        <?php echo $form->error($model,'link'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'type'); ?>
        <?php echo $form->dropDownList($model,'type', array(2 => 'Слайд-шоу', 'Флеш-ролик')); ?>
        <?php echo $form->error($model,'type'); ?>
    </div>

    <?php if ($src = $model->src): ?>
    <div class="row">
	    <?php if($model->type != Banner::BANNER_FLASH): ?>
            <img src="<?php echo $src; ?>" alt="" style="max-width:100%" />
	    <?php else: ?>
		    <?php
			    $width = isset(Yii::app()->params['banner']['flash']['width']) ? Yii::app()->params['banner']['flash']['width'] : 240;
			    $height = isset(Yii::app()->params['banner']['flash']['height']) ? Yii::app()->params['banner']['flash']['height'] : 320;
		    ?>
		    <div class="clr"></div>
		    <div style="height:20px"></div>
		    <div id="banner"></div>
		    <script type="text/javascript">
			    $(function(){
				    $('#banner').flash({
					    src: <?php printf("'%s%s'", Yii::app()->baseUrl, $model->src); ?>,
					    width: <?php echo $width; ?>,
					    height: <?php echo $height; ?>
				    });
			    });
		    </script>
		    <div class="clr"></div>
	    <?php endif; ?>
    </div>
    <div class="row">
        <a id="change-file" class="js-link">Сменить</a>
    </div>
    <script type="text/javascript">
        $(function(){
            $('#file_field').hide();
            $('#change-file').click(function() {
                $('#file_field').show();
                $(this).remove();
            });
        });
    </script>
    <?php endif; ?>

    <div class="row" id="file_field">
        <?php echo $form->labelEx($model,'file'); ?>
        <?php echo $form->fileField($model, 'file'); ?>
        <?php echo $form->error($model,'file'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'default-button')); ?>
        <?php echo CHtml::link('Отмена', array('index')); ?>
    </div>

    <?php $this->endWidget(); ?>
</div><!-- form -->
