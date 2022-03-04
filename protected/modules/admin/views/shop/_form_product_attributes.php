<script type="text/javascript">
	$(document).ready(function(){
		window.attributes = [];

		<?php if(!$model->isNewRecord):?>
			<?php $productAttributes = $model->productAttributes; foreach ($productAttributes as $productAttribute):?>
				window.attributes.push(<?php echo "'".$productAttribute->eavAttribute['name']."'";?>);
			<?php endforeach;?>
			<?php else:?>
			<?php foreach ($fixAttributes as $fixAttribute):?>
				window.attributes.push(<?php echo "'".$fixAttribute['name']."'";?>);
			<?php endforeach;?>
		<?php endif;?>
		
	});
</script>
<div class="row">
	<label>Введите название атрибута</label>
	<?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
	    'model'=> EavAttribute::model(), // модель
  		'attribute'=>'name', // атрибут модели
  		'name'=>'searchAttribute',
  		'id'=>'searchAttribute',
	    'source'=>$this->createUrl('attributes/autocomplete'),
	    // additional javascript options for the autocomplete plugin
	    'options'=>array(
	        'minLength'=>'2',
	        'select'=>"js:function(event, ui) {
	        	if(jQuery.inArray( ui.item.value, window.attributes ) == -1){
	        		window.attributes.push(ui.item.value);
	        		$('#attributes').append('<div class=row><label>' + ui.item.value + '</label><input type=text name=EavValue[' + ui.item.id + ']></div>');
	        	}

	        	$('#searchAttribute').val('');
	        	return false;
	        }",
	    ),
	    'htmlOptions'=>array(
	    	'class'=>'form-control'
	    ),
		));
	?>
</div>
<hr>
<br>
<div id="attributes">
	<?php 
	$fixeded = array();
	if(!$model->isNewRecord) {
		foreach ($productAttributes as $productAttribute) 
			$fixeded[] = $productAttribute->eavAttribute['name'];
    }
	foreach ($fixAttributes as $fixAttribute):
		if(!in_array($fixAttribute['name'], $fixeded)): ?>
		<div class="row">
			<label><?php echo $fixAttribute['name'];?></label>
			<input type=text name="EavValue[<?php echo $fixAttribute['id'];?>]" value="">
		</div>
	<?php 
		endif;	
	endforeach;

	if(!$model->isNewRecord): 
		foreach ($productAttributes as $productAttribute):?>
			<div class="row">
				<label><?php echo $productAttribute->eavAttribute['name'];?></label>
				<input type=text name="EavValue[<?php echo $productAttribute->eavAttribute['id'];?>]" value="<?php echo $productAttribute['value'];?>">
			</div>
		<?php endforeach;?>
	<?php endif;?>
</div>