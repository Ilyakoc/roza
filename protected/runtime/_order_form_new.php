<?php
/* @var Controller $this */
/* @var Order $model */

$payments = $model->getPaymentTypes();

$noticeList = [
	'Анонимная доставка' => 'Анонимная доставка',
	'Не перезванивать мне' => 'Не перезванивать мне',
	'При отсутствии получателя оставить родственникам, соседям, коллегам' => 'При отсутствии получателя оставить родственникам, соседям, коллегам',
];

if (!$model->payment) {
	$model->payment = 1;
}
if (!$model->delivery) {
	$model->delivery = 2;
}
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
				<div class="summary-delprice-wrap" style="visibility: hidden;">
					<div style="padding-left: 15px;color: #000;font-size: 14px;line-height: 40px;">Стоимость доставки: <span class="summary-delprice"></span> рублей</div>
				</div>
			</div>

			<?php if (false): ?>
				<div class="Order_address1 js-delivery-only">
					<?php echo $form->textArea($model, 'address', array('placeholder'=>'Адрес доставки','class'=>'order70')); ?>
					<?php echo $form->error($model, 'address'); ?>
				</div>
			<?php endif ?>
		</div>
	</div>
    <div class="separatorNew"></div>
	<div class="priceNew">
		<div class="basket-result-table">
		    <div class="b-r-t-3">
			<?
			// echo "<pre>";
			// var_dump(CmsCart::getInstance());
			// echo "</pre>";
			?>
		       <span class="brPrice"> Итого: </span><span class="b-r-t-3-summary"><?php echo CmsCart::getInstance()->priceAll(); ?></span>
		    </div>
		</div>
	</div>

	<div class="form-block">
		<h2 class="form-block-title">Заполните форму</h2>
		<div class="form-block-content">
			<div class="send">
				<div class="send-frist-wrap" style="height: 100%;	">
				<h4>Отправитель</h4>
					<div>
						<h6 class="input__title">Имя</h6>
						<?php echo $form->textField($model, 'name', array()); ?>
						<?php echo $form->error($model, 'name'); ?>
					</div>				
					<div>
						<h6 class="input__title">Email</h6>
						<?php echo $form->textField($model, 'email', array()); ?>
						<?php echo $form->error($model, 'email'); ?>
					</div>
					<div>
						<h6 class="input__title">Телефон</h6>
						<?php echo $form->textField($model, 'phone', array()); ?>
						<?php echo $form->error($model, 'phone'); ?>
					</div>
					<div style="display:none;">
						<h6 class="input__title">Текст на открытке</h6>
						<?php echo $form->textArea($model, 'card_text', array()); ?>
						<?php echo $form->error($model, 'card_text'); ?>
					</div>
					<p href="#" class="send_link"><span>Пожелания к заказу / текст на открытке</span></p>
					<div style="margin-bottom: 44px;">
						<?php echo $form->textArea($model, 'comment', array()); ?>
						<?php echo $form->error($model, 'comment'); ?>
					</div>
					<div>
						<label class="containerCheck">
							<p class="title_checkbox_posdata">Cогласен на обработку персональных данных</p>
							<?php echo $form->checkBox($model, 'personal', array('class'=>'inline', 'label'=>'test', 'labelOptions'=>array('class'=>'dib dost-label'), 'template'=>'{beginLabel}{input}{labelTitle}{endLabel}', 'separator'=>'')); ?>
							<span class="checkmark" style="border-radius: 0%;"></span>
							<?php echo $form->error($model, 'personal'); ?>
						</label>
					</div>
					
					</div>
					<div class="form-block-content-check" style="display:none;">
						<?php echo $form->checkBoxList($model, 'notice', $noticeList, array('class'=>'inline', 'labelOptions'=>array('class'=>'containerCheck'), 'template'=>'{beginLabel}{labelTitle}{input}<span class="checkmark"></span>{endLabel}', 'separator'=>'')); ?>
						<?php echo $form->error($model, 'notice'); ?>
					</div>
			</div>
			<div class="receive">
				<h4>Получатель</h4>
					<label class="containerCheck">
						Уточнить у получателя
						<input type="checkbox" name="check_time" value="1" class="check-buyer">
						<span class="checkmark"></span>
					</label>
					<div>
						<h6 class="input__title not_necessary" id="Order_recipient_name_text">Имя</h6>
						<?php echo $form->textField($model, 'recipient_name', array()); ?>
						<?php echo $form->error($model, 'recipient_name'); ?>
					</div>
					<div>
						<h6 class="input__title not_necessary" id="Order_recipient_date_text">Дата доставки</h6>
						<?php echo $form->textField($model, 'recipient_date', array()); ?>
						<?php echo $form->error($model, 'recipient_date'); ?>
					</div>
					<div>
						<h6 class="input__title not_necessary" id="Order_recipient_phone_text">Телефон</h6>
						<?php echo $form->textField($model, 'recipient_phone', array()); ?>
						<?php echo $form->error($model, 'recipient_phone'); ?>
					</div>

					<div class="receive-seclect">
						<h6 class="not_necessary" id="Order_time_text">Время доставки</h6>
						<?php echo $form->dropDownList($model, 'time', CHtml::listData(Time::model()->findAll(['order' => 'sort']), 'title', 'title'), ['empty' => 'Выберите время доставки']); ?>
						<?php echo $form->error($model, 'time'); ?>
					</div>


					<div class="js-delivery-only">
						<div>
							<h6 class="input__title not_necessary" id="Order_address_text">Адрес доставки</h6>
							<?php echo $form->textField($model, 'address', array('class' => 'last-inp')); ?>
							<?php echo $form->error($model, 'address'); ?>
						</div>

					</div>		
			</div>
		</div>
		<div class="btn_block">
			<h4>Способ оплаты</h4>

			<?php echo $form->radioButtonList($model, 'payment', $payments, array('labelOptions'=>array('class'=>'containerCheck'), 'template'=>'{beginLabel}{labelTitle}{input}<span class="checkmark"></span>{endLabel}', 'separator'=>'')); ?>
			<?php echo $form->error($model, 'payment'); ?>

		</div>

		<?= $form->errorSummary($model) ?>

	</div>

	<?php if (false): ?>
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
			        <?php echo CHtml::submitButton('Отправить',array('class'=>'goobasket-btn send-form-with-prosdata', 'style'=>'border:none')); ?>
				</div>
			    </div>
			</div>
		</div>
	<?php endif ?>


    <?php $this->endWidget(); ?>
</div>


<script>
	$( document ).ready(function() {
		$('#Order_delivery input:checked').closest(".dib").addClass("act");
		
		$('.psevdosubmit .c').on('click',function(){
			if($('#Order_delivery input[type=radio]:checked').val() == 2){
				if($('#Order_area').val() == ""){
					$("html, body").animate({scrollTop: $("header").height()},"slow");
					$(".js-delivery-only .errorMessage").css("display","block");
					$(".js-delivery-only .errorMessage").text("Необходимо заполнить поле «Район».");
				}else{
					$(".js-delivery-only .errorMessage").css("display","none");
					$(".js-delivery-only .errorMessage").text("");
					$('.psevdosubmithide').trigger('click');
					// setTimeout(function(){
					  // $('.psevdosubmithide').trigger('click');
					// }, 1000);
				}
			}else{
				$(".js-delivery-only .errorMessage").css("display","none");
				$(".js-delivery-only .errorMessage").text("");
				$('.psevdosubmithide').trigger('click');
				// setTimeout(function(){
				  // $('.psevdosubmithide').trigger('click');
				// }, 1000);
			}
		})
		
		$('.check-buyer').change(function() {
            var $self = $(this);
            var $target = $($self.data('target'));
			var arrCheck = ['#Order_recipient_name','#Order_recipient_date','#Order_recipient_phone','#Order_time','#Order_address'];
			var arrCheckText = ['#Order_recipient_name_text','#Order_recipient_date_text','#Order_recipient_phone_text','#Order_time_text','#Order_address_text'];
			
			$.each(arrCheck,function(index,value){
				if($self.prop('checked')) {
					//$(value).val('Уточнить у получателя').prop('readonly', true);
					//$(value).val('Уточнить у получателя').css({'pointer-events': 'none', 'opacity': '0.5', 'position': 'relative','z-index': '100','color': 'transparent',});
					$(value).val('Уточнить у получателя').addClass('event-none_order');
				} else {
					$(value).val('').removeClass('event-none_order');
				}
			});
			$.each(arrCheckText,function(index,value){
				if($self.prop('checked')) {
					//$(value).val('Уточнить у получателя').prop('readonly', true);
					//$(value).css('opacity','0.5');
					$(value).addClass('event-none_order_text');
				} else {
					$(value).removeClass('event-none_order_text');
				}
			});

        });

		$('.send_link').on('click',function(){
			$('#Order_comment').toggleClass('db');
		})

		$('#Order_delivery input[type=radio]').change(function() {
	        if (this.value == '1') {
				$(".btn_block .psevdosubmit").css("display","none");
				$(".btn_block .psevdosubmithide").css("display","block");
	            $('.Order_address').fadeOut();
	            $('.Order_address textarea').val('');
	        }
	        else if (this.value == '2') {
				if($('#Order_area').val() == ""){
					$(".btn_block .psevdosubmit").css("display","block");
					$(".btn_block .psevdosubmithide").css("display","none");
				}else{
					$(".btn_block .psevdosubmit").css("display","none");
					$(".btn_block .psevdosubmithide").css("display","block");
				}
	            $('.Order_address').fadeIn();
	        }
	    });

		$('input.inline[type="radio"]').on('change', function() {
			var self = $(this);
			
			if (self.val() == 2) {
				$('.js-delivery-only').show();
				$('.receive').show();

				$.post('/shop/setDelivery', {id: $('#Order_area').val()}, function(price) {
					$('.b-r-t-3-summary').text(price);
				});
				$.post('/shop/SetDeliveryPrice', {id: $('#Order_area').val()}, function(priceDel) {
					$('.summary-delprice-wrap').css("visibility","inherit");
					$('.summary-delprice-wrap .summary-delprice').text(priceDel);
				});
			} else {
				$('.js-delivery-only').hide();
				$('.receive').hide();

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
			$.post('/shop/SetDeliveryPrice', {id: $('#Order_area').val()}, function(priceDel) {
				$('.summary-delprice-wrap').css("visibility","inherit");
				$('.summary-delprice-wrap .summary-delprice').text(priceDel);
			});
		});

		$('#Order_recipient_phone, #Order_phone').mask('+7 (999) 999-99-99');

	});
</script>
