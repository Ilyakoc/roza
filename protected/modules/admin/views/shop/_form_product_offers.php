<?php
/**
 * @var $model Product
 * @var $form CActiveForm
 */
?>

<div class="form-group">
    <?= CHtml::link('Добавить торговое предложение', ['/admin/offer/create', 'product_id' => $model->id], ['class' => 'default-button']) ?>
</div>

<br>

<ul class="list-unstyled">
    <?php foreach ($model->offers as $offer): ?>
        <li class="js-offer-item clear">
            <span><?= $offer->title ?></span>

            <span class="pull-right">
                <?= CHtml::link('Редактировать', ['/admin/offer/update', 'id' => $offer->id], ['class' => 'js-offer-update']) ?>
                <?= CHtml::link('Удалить', ['/admin/offer/delete', 'id' => $offer->id], ['class' => 'js-offer-delete']) ?>
            </span>
        </li>
    <?php endforeach; ?>
</ul>

<script>
    $(function() {
        $('.js-offer-delete').click(function () {
            var el = $(this);

            if (!confirm('Вы действительно хотите удалить торговое предложение?')) {
                return false;
            }

            $.post(el.attr('href'), {}, function() {
                el.closest('.js-offer-item').remove();
            });

            return false;
        });
    })
</script>
