<?php
if($categoryList):
?>
<style type="text/css">
	select{width: 96%;}
</style>
<div class="row chosen">
	<label>Выберите дополнительные категории</label>
	<?php
		$this->widget('ext.chosen.Chosen',array(
		   'name' => 'relatedCategories', // input name
		   'multiple' => true,
		   'placeholderMultiple' => 'Выберите дополнительные категории',
		   'data' => CHtml::listData($categoryList, 'id', 'title'),
		));
	?>
</div>

<?php if(!$model->isNewRecord && $model->relatedCategories && $relatedCategories):?>
<div class="row related">
	<?php foreach($relatedCategories as $item):?>
		<?php if($item['id'] != $model->category_id):?>
			<div class="item">
				<?php echo $item['title'];?> <a href="#" class="remove-related" data-id="<?php echo $model->id;?>" data-related="<?php echo $item['id'];?>">Удалить</a>
			</div>
		<?php endif;?>
	<?php endforeach;?>
</div>

<script type="text/javascript">
	$(function(){
		$('.remove-related').click(function(){
			if(!confirm('Вы действительно хотите удалить дополнительную категорию?')) return false;

			var self = $(this);

			var d = {
				'product': self.data('id'),
				'related': self.data('related')
			};

			$.post('/admin/shop/removeRelatedCategory', {data: d}, function(){
				self.closest('.item').remove();
			});

			return false;
		});
	});
</script>
<?php endif;?>

<?php endif; ?>