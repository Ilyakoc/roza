<?php $this->pageTitle = 'Новый товар - '. $this->appName; ?>

<h1>Новый товар</h1>
<?php echo $this->renderPartial('_form_product', compact('model', 'fixAttributes')); ?>
<script>
$( document ).ready(function() {
    $('#Product_code').val(Math.floor((Math.random() * 10000000000) + 1));
});
</script>
