<?php
/**
 * @var $model CActiveRecord
 */
?>
<p>Здравствуйте, <?php echo $model->name; ?></p>
<p>Заказ <?php echo '№'. $model->id; ?></p>

<?php foreach($model->attributes as $name=>$value) { ?>
<?php if (!key_exists($name, $model->attributeLabels()) || $name == 'name' || $name == 'email' || $name == 'products' || empty($value)) continue; ?>
<p>
    <strong><?php echo $model->getAttributeLabel($name); ?></strong>:<br />
    <?php echo $name == 'created' ? $model->date : $value; ?>
</p>
<?php } ?>

<h4>Товары</h4>
<ol>
    <?php foreach(CmsCart::getInstance()->getResult(true) as $p) { ?>
    <li><?php printf('%s / %s / %s (%d руб) x %d шт = %d руб', $p->obj->code, ($p->obj->offer_title ? $p->title . ' - ' . $p->obj->offer_title : $p->title), $p->obj->category->title, $p->order_price, $p->count, $p->count * $p->order_price); ?></li>
    <?php } ?>
</ol>

<h4>Итого: <?php echo CmsCart::getInstance()->priceAll() + $model->delivery_price; ?> руб.</h4>
<p>В ближайшее время наш менеджер с Вами свяжется, если этого не произошло или у Вас возникли вопросы, позвоните по телефону 239-66-75 или 8-983-310-66-75</p>
