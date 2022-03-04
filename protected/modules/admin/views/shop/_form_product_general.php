
    <div class="row">
        <?php echo $form->labelEx($model, 'category_id'); ?>
        <?php echo $form->dropDownList($model, 'category_id', $model->categories); ?>
        <?php echo $form->error($model, 'category_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'title'); ?>
        <?php echo $form->textField($model, 'title'); ?>
        <?php echo $form->error($model, 'title'); ?>
    </div>
    
    <div class="row">
    	<? $this->widget('admin.widget.Alias.AliasFieldWidget', compact('form', 'model')); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'code'); ?>
        <?php echo $form->textField($model, 'code'); ?>
        <?php echo $form->error($model, 'code'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'description'); ?>
        <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array(
            'model'=>$model,
            'attribute'=>'description',
            'htmlOptions'=>array('class'=>'small')
        )); ?>
        <?php echo $form->error($model, 'description'); ?>
    </div>

    <style>
        .row-table table.mceLayout {
            width: 100% !important;
        }
    </style>

    <div class="row row-table">
        <?php echo $form->label($model, 'composition'); ?>
        <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array('model'=>$model, 'attribute'=>'composition', 'full'=>false)); ?>
        <?php echo $form->error($model,'composition'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'price'); ?>
        <?php echo $form->textField($model, 'price', array('class'=>'w10 inline')); ?> руб.
        <?php echo $form->error($model, 'price'); ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model, 'old_price'); ?>
        <?php echo $form->textField($model, 'old_price', array('class'=>'w10 inline')); ?> руб.
        <?php echo $form->error($model, 'old_price'); ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model, 'weight'); ?>
        <?php echo $form->textField($model, 'weight', array('class'=>'w10 inline')); ?> 
        <?php echo $form->error($model, 'weight'); ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model, 'size'); ?>
        <?php echo $form->textField($model, 'size', array('class'=>'w10 inline')); ?>
        <?php echo $form->error($model, 'size'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'diameter'); ?>
        <?php echo $form->textField($model, 'diameter', array('class'=>'w10 inline')); ?>
        <?php echo $form->error($model, 'diameter'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'height'); ?>
        <?php echo $form->textField($model, 'height', array('class'=>'w10 inline')); ?>
        <?php echo $form->error($model, 'height'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'sale_value'); ?>
        <?php echo $form->textField($model, 'sale_value', array('class'=>'w10 inline')); ?>
        <?php echo $form->error($model, 'sale_value'); ?>
    </div>

    <div class="row">
        <?php echo $form->checkBox($model, 'notexist'); ?>
        <?php echo $form->labelEx($model, 'notexist', array('class'=>'inline')); ?>
        <?php echo $form->error($model, 'notexist'); ?>
    </div>

    <div class="row">
        <?php echo $form->checkBox($model, 'sale'); ?>
        <?php echo $form->labelEx($model, 'sale', array('class'=>'inline')); ?>
        <?php echo $form->error($model, 'sale'); ?>
    </div>

    <div class="row">
        <?php echo $form->checkBox($model, 'new'); ?>
        <?php echo $form->labelEx($model, 'new', array('class'=>'inline')); ?>
        <?php echo $form->error($model, 'new'); ?>
    </div>
<!--
    <div class="row">
        <?php echo CHtml::link('Управление эскизами', array('shop/thumbsUpdate/', 'id' => $model->id)); ?>
    </div>
-->
    <div class="row">
        <?php echo $form->labelEx($model, 'mainImg'); ?>
        <?php if ($mainImg = $model->getMainImg(true)): ?>
            <div id="mainImg" class="mainImg modelImages">
                <div class="img">
                    <a class="remove-icon" href="<?php echo $this->createUrl('shop/removeMainImg'); ?>" onclick="return AdminShop.removeMainImg(<?php echo $model->id; ?>, this);"></a>
                    <img src="<?php echo $mainImg; ?>" alt="" />
                </div>
                <p>
                    <a class="js-link" onclick="$(this).parents('.row').find(':file').toggleClass('hidden');">Изменить</a>
                </p>
            </div>
        <?php endif; ?>
        <?php echo $form->fileField($model, 'mainImg', $mainImg ? array('class'=>'hidden'): array()); ?>
        <?php echo $form->error($model, 'mainImg'); ?>
        <script type="text/javascript">
            $(function() {
                $('#mainImg .img').hover(function() {
                    $(this).toggleClass('hover');
                });
            });
        </script>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'moreImg'); ?>
        <div class="non-required <?php if (!count($model->moreImages)) echo ' hidden'; ?>">
            <?php echo $form->fileField($model, 'moreImg[]'); ?>
            <a id="addSubImg" class="js-link">Добавить</a>
            <div class="added-files"></div>
            <?php echo $form->error($model, 'moreImg'); ?>
            <?php $this->widget('widget.adminImages.adminImages', array('model'=>$model, 'viewImages'=>'onlyimages')); ?>
        </div>
        <script type="text/javascript">
            $(function() {
                var clone = $('#addSubImg').prev().clone().attr({id: ''});
                $('#addSubImg').click(function() {
                    var new_clone = clone.clone();
                    var div   = $('<div></div>').addClass('ufile');
                    var dlink = $('<a></a>').addClass('js-link').text('Удалить').click(function() {
                        $(this).parent().remove();
                    });
                    $(new_clone).appendTo(div);
                    $(dlink).appendTo(div);
                    $(div).appendTo($(this).next());
                });
            });
        </script>
    </div>
    <?php /* ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'property'); ?>
        <div class="non-required hidden">
            <?php echo $form->textField($model, 'property', array('class'=>'w35')); ?>
            <?php echo $form->error($model, 'property'); ?>
        </div>
    </div>

    <?php */ ?>
