<h1>Перенос товаров</h1>

<div class="form">
	<form action="" method="get">
		<div class="row">
			<label for="from">Откуда</label>
			<?= CHtml::dropDownList('from', $from, Product::getCategories(), ['empty' => '']) ?>
		</div>
	</form>
	<form action="" method="post">
		<div class="row">
			<label for="to">Куда</label>
			<?= CHtml::dropDownList('to', $to, Product::getCategories(), ['empty' => '']) ?>
		</div>
		
		<div class="row">
			<input type="submit" value="Перенести" class="default-button">
		</div>

		<?php if ($products): ?>
			<table>
				<tr>
					<td><input class="js-toggle-all" type="checkbox" name="all" value="" checked></td>
					<td></td>
				</tr>
				<?php foreach ($products as $product): ?>
					<tr>
						<td><input type="checkbox" name="product[]" value="<?= $product->id ?>" class="js-product-input" checked></td>
						<td><?= $product->title ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php endif ?>

		
	</form>
</div>

<p style="color: green;"><?= Yii::app()->user->getFlash('message') ?></p>

<script>
	$(function() {
		$('.js-toggle-all').change(function() {
			var self = $(this);
			$('.js-product-input').prop('checked', self.prop('checked'));
		});

		$('#from').change(function() {
			$(this).closest('form').submit();
		});
	});
</script>
