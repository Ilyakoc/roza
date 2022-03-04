<style>
	.row-table table.mceLayout {
		width: 100% !important;
	}
</style>

<div class="row row-table">
    <?php echo $form->label($model, 'quality_text'); ?>
    <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array('model'=>$model, 'attribute'=>'quality_text', 'full'=>false)); ?>
    <?php echo $form->error($model,'quality_text'); ?>
</div>

<div class="row row-table">
    <?php echo $form->label($model, 'return_text'); ?>
    <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array('model'=>$model, 'attribute'=>'return_text', 'full'=>false)); ?>
    <?php echo $form->error($model,'return_text'); ?>
</div>

<div class="row row-table">
    <?php echo $form->label($model, 'delivery_text'); ?>
    <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array('model'=>$model, 'attribute'=>'delivery_text', 'full'=>false)); ?>
    <?php echo $form->error($model,'delivery_text'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'delivery_price1'); ?>
    <?php echo $form->textField($model, 'delivery_price1'); ?>
    <?php echo $form->error($model,'delivery_price1'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'delivery_price2'); ?>
    <?php echo $form->textField($model, 'delivery_price2'); ?>
    <?php echo $form->error($model,'delivery_price2'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'delivery_price3'); ?>
    <?php echo $form->textField($model, 'delivery_price3'); ?>
    <?php echo $form->error($model,'delivery_price3'); ?>
</div>

<div class="row row-table">
    <?php echo $form->label($model, 'delivery_text1'); ?>
    <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array('model'=>$model, 'attribute'=>'delivery_text1', 'full'=>false)); ?>
    <?php echo $form->error($model,'delivery_text1'); ?>
</div>


<div class="row row-table">
    <?php echo $form->label($model, 'delivery_text2'); ?>
    <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array('model'=>$model, 'attribute'=>'delivery_text2', 'full'=>false)); ?>
    <?php echo $form->error($model,'delivery_text2'); ?>
</div>


<div class="row row-table">
    <?php echo $form->label($model, 'delivery_text3'); ?>
    <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array('model'=>$model, 'attribute'=>'delivery_text3', 'full'=>false)); ?>
    <?php echo $form->error($model,'delivery_text3'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'away_area_map'); ?>
    <?php echo $form->textArea($model, 'away_area_map'); ?>
    <?php echo $form->error($model,'away_area_map'); ?>
</div>

<div class="row row-table">
    <?php echo $form->label($model, 'away_area_text'); ?>
    <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array('model'=>$model, 'attribute'=>'away_area_text', 'full'=>false)); ?>
    <?php echo $form->error($model,'away_area_text'); ?>
</div>

