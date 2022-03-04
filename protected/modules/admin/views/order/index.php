<?php
/**
 * File: index.php
 * User: Mobyman
 * Date: 28.01.13
 * Time: 12:26
 */
?>
<script type="text/javascript">
    $(function() {
        $('#orders').on('click', '.orderuser', function(){
           $("table#orders").find(".details[data-item='" + $(this).data('item') + "']").toggle();
           $("table#orders").find(".order[data-item='" + $(this).data('item') + "']").find('.sumprice').toggleClass('actsum')
        });
        $("#orders").on('click', '.mark', function(){
            t = $(this);
            $.ajax({
              type: "POST",
              url: "<?php echo Yii::app()->createUrl("/admin/order/ajax"); ?>",
              data: {item: $(this).data('item'), action: "completed"},
              dataType: "json",
              success: function(data) {
                  if(!data.status) {
                    $(t).removeClass('unmarked');
                  } else {
                    $(t).addClass('unmarked');
                  }
                  $('#orderscount').text(data.count);
              }
            });
        })
        $("#orders").on('blur', '.comment', function(){
            t = $(this);
            $.ajax({
              type: "POST",
              url: "<?php echo Yii::app()->createUrl("/admin/order/ajax"); ?>",
              data: {item: $(this).data('item'), action: "comment", comment: t.val()},
              dataType: "json",
              success: function(data) {
                  t.val(data.status);
              }
            });
        });
    });
</script>

<h1>Заказы</h1>
<table id="orders">

    <tr class="head">
        <td>№</td>
        <td style="border-right:none; width:305px;">ФИО, контакты</td>
        <td style="border:none;width:35px;"></td>
        <td>Сумма</td>
        <td>Дата</td>
        <td>Статус</td>
    </tr>

    <?php foreach($model as $item): ?>
	<?
// echo "<pre>";
// var_dump($item);
// echo "</pre>";
?>
        <tr class="order" data-item="<?php echo $item->id; ?>">
            <td class="number"><?php echo $item->id; ?>.</td>
            <td colspan="2">
                    <?php echo CHtml::link($item->name, "#", array('class' => 'orderuser', 'data-item' => $item->id)); ?>,
                    <?php echo $item->email; ?>,
                    <?php echo $item->phone; ?>,
                    <?php echo $item->address; ?>.
                    <?php if($item->area): ?>
                      <br /><b>Район:</b> <?php echo $item->area; ?>
                    <?php endif; ?>
                    <?php if($item->payment): ?>
                    	<br /><b>Способ оплаты:</b> <?php echo $item->payment; ?>
                    <?php endif; ?>
                    <?php if($item->delivery): ?>
                    	<br /><b>Способ получения:</b> <?php echo $item->getDelivery(); ?>
                    <?php endif; ?>
                    <?php if($item->delivery_price): ?>
                      <br /><b>Стоимость доставки:</b> <?php echo $item->delivery_price; ?> руб.
                    <?php endif; ?>
					
					<?php if((int)($item->delivery) == 2): ?>
						<?php if($item->recipient_name): ?>
						  <br /><b>Имя получателя:</b> <?php echo $item->recipient_name; ?>
						<?php endif; ?>
						<?php if($item->recipient_phone): ?>
						  <br /><b>Телефон получателя:</b> <?php echo $item->recipient_phone; ?>
						<?php endif; ?>
						<?php if($item->recipient_date): ?>
						  <br /><b>Дата доставки:</b> <?php echo $item->recipient_date; ?>
						<?php endif; ?>
						<?php if($item->time): ?>
						  <br /><b>Время доставки:</b> <?php echo $item->time; ?>
						<?php endif; ?>
					<?php endif; ?>
            </td>

            <td class="sumprice"><?php echo $item->summaryPrice; ?> р.</td>
            <td><?php echo $item->date; ?></td>
            <td><div class="mark <?php echo !$item->completed ? 'marked' : 'unmarked'; ?>" data-item="<?php echo $item->id; ?>"></div></td>
        </tr>
        <?php if (($products = $item->getProducts())) foreach ($products as $product): ?>
            <tr class="details" data-item="<?php echo $item->id; ?>">
                <td colspan="2"><?php echo CHtml::link($product->title, array('/shop/product', 'id'=>$product->id), array('target'=>'_blank')) ?></td>
                <td class="count"><?php echo $product->count; ?></td>
                <td class="sum"><?php echo $product->order_price; ?> р.</td>
                <td colspan="2"></td>
            </tr>
        <?php endforeach; ?>
            <tr class="details" data-item="<?php echo $item->id; ?>">
                <td colspan="6"><textarea data-item="<?php echo $item->id; ?>" class="comment"><?php echo $item->comment; ?></textarea></td>
            </tr>
    <?php endforeach; ?>
</table>

<?php $this->widget('CLinkPager', array(
    'pages' => $pages,
)); ?>

