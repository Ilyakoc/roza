<?php
/**
 * @var $model CActiveRecord
 */
?>
<h3>Новый заказ с сайта <?php Yii::app()->name ?></h3>

<?php foreach($model->attributes as $name=>$value) { ?>
<?php if (!key_exists($name, $model->attributeLabels()) || $name == 'products' || empty($value)) continue; ?>
<p>
    <strong><?php echo $model->getAttributeLabel($name); ?></strong>:<br />
    <?php
    if ($name == 'created') {
    	echo $model->date;
    } elseif ($name == 'delivery') {
    	echo $model->getDelivery($value);
    } else {
    	echo $value;
    }
    ?>
    <?php #echo $name == 'created' ? $model->date : $value; ?>
    <?php #echo $name == 'delivery' ? $model->getDelivery($value) : $value; ?>
</p>
<?php } ?>

<h4>Товары</h4>
<ol>
    <?php foreach(CmsCart::getInstance()->getResult(true) as $p) { ?>
    <li><?php printf('%s / %s / %s (%d руб) x %d шт = %d руб', $p->obj->code, ($p->obj->offer_title ? $p->title . ' - ' . $p->obj->offer_title : $p->title), $p->obj->category->title, $p->order_price, $p->count, $p->count * $p->order_price); ?></li>
    <?php } ?>
</ol>

<h4>Итого: <?php echo CmsCart::getInstance()->priceAll() + $model->delivery_price; ?> руб.</h4>
