<?php $this->pageTitle = 'Ошибка - '. $this->appName; ?>
<h1>Ошибка <?php echo $code; ?></h1>

<div class="error">
<?php echo CHtml::encode($message); ?>
</div>
