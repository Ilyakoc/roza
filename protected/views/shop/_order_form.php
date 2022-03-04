<?php
/* @var Controller $this */
/* @var Order $model */
?>



<!-- <h1>Оформление заказа</h1> -->
<div class="" >
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'order-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ),
    )); /* @var CActiveForm $form */ ?>


	<div class="delivery__new">
		<h3 class="desk">Выберите способ доставки:</h3>
		<div class="del__flex">
			<div class="label-radio-group">
				<h3 class="mod__h3">Выберите способ доставки:</h3>
				<?php echo $form->radioButtonList($model, 'delivery', $model->getDeliveryTypes(), array('class'=>'inline', 'labelOptions'=>array('class'=>'dib dost-label'), 'template'=>'{beginLabel}{input}{labelTitle}{endLabel}', 'separator'=>'')); ?>
				<?php echo $form->error($model, 'delivery'); ?>
			</div>

			<div class="div__list js-delivery-only">
				<div class="section">
	                <?php echo $form->error($model, 'area'); ?>
	                <?php echo $form->hiddenField($model, 'area', array('class'=>'section-input')); ?>
	                <div class="section-header">
	                    <div data-title="Выбор района" class="section-title">
	                        <p>
	                            <span class="section-title__name"></span>
	                            <span class="fa-triangle"></span>
	                        </p>
	                    </div>
	                </div>
	                <ul class="section-body">
	                	<?php foreach (Area::model()->findAll(['order' => 'sort']) as $order): ?>
		                    <li data-select="<?= $order->id ?>"><?= $order->title ?></li>
	                	<?php endforeach; ?>
	                </ul>
	            </div>
			</div>

			<div class="Order_address1 js-delivery-only">
				<?php echo $form->textArea($model, 'address', array('placeholder'=>'Адрес доставки','class'=>'order70')); ?>
				<?php echo $form->error($model, 'address'); ?>
			</div>
		</div>
	</div>
    <div class="separatorNew"></div>
	<div class="priceNew">
		<div class="basket-result-table">
		    <div class="b-r-t-3">
		       <span class="brPrice"> Итого: </span><span class="b-r-t-3-summary"><?php echo CmsCart::getInstance()->priceAll(); ?></span>
		    </div>
		</div>
	</div>


<div class="cart__bot">
	<div class="opl">
		<h3>Способ Оплаты</h3>
		<?php if ($model->scenario == 'payment'): ?>
	    <div class="label-radio-group">
	        <?php echo $form->radioButtonList($model, 'payment', $model->getPaymentTypes(), array('class'=>'inline', 'labelOptions'=>array('class'=>'dib dost-label'), 'template'=>'{beginLabel}{input}{labelTitle}{endLabel}', 'separator'=>'')); ?>
	        <?php echo $form->error($model, 'payment'); ?>
	    </div>
	    <?php endif; ?>
	</div>
	<div class="peop">
		<h3>Заказчик</h3>
		<div class="tabl">
			<div class="tabl__item">
				<div class="inp-wrap">
						<?php echo $form->textField($model, 'name', array('placeholder'=>'Ваше имя*','class'=>'inp100')); ?>
						<?php echo $form->error($model, 'name'); ?>
					</div>
			</div>
			<div class="tabl__item">
				<div class="inp-wrap">
					<?php echo $form->textField($model, 'phone', array('placeholder'=>'Телефон*','class'=>'inp100')); ?>
					<?php echo $form->error($model, 'phone'); ?>
				</div>
			</div>
			<div class="tabl__item">
				<div class="row">
					<?php echo $form->textField($model, 'email', array('placeholder'=>'Email*','class'=>'inp100')); ?>
					<?php echo $form->error($model, 'email'); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="mess">
		<h3>Комментарий:</h3>
		<div class="mess__inner">
		<?php echo $form->textArea($model, 'comment', array('placeholder'=>'Комментарий к заказу','class'=>'order70')); ?>
		<?php echo $form->error($model, 'comment'); ?>

	    <div class="row buttons">
	        <?php echo CHtml::submitButton('Отправить',array('class'=>'goobasket-btn', 'style'=>'border:none')); ?>
		</div>
	    </div>
	</div>
</div>


    <?php $this->endWidget(); ?>
</div>


<script>
	$( document ).ready(function() {

		$('#Order_delivery input[type=radio]').change(function() {
	        if (this.value == '1') {
	            $('.Order_address').fadeOut();
	            $('.Order_address textarea').val('');
	        }
	        else if (this.value == '2') {
	            $('.Order_address').fadeIn();
	        }
	    });

		$('input.inline[type="radio"]').on('change', function() {
			var self = $(this);
			
			if (self.val() == 2) {
				$('.js-delivery-only').show();

				$.post('/shop/setDelivery', {id: $('#Order_area').val()}, function(price) {
					$('.b-r-t-3-summary').text(price);
				});
			} else {
				$('.js-delivery-only').hide();

				$.post('/shop/setDelivery', {id: 0}, function(price) {
					$('.b-r-t-3-summary').text(price);
				});
			}
		});

		$('#Order_area').on('change', function() {
			var self = $(this);
			
			$.post('/shop/setDelivery', {id: self.val()}, function(price) {
				$('.b-r-t-3-summary').text(price);
			});
		});

	});
</script>
