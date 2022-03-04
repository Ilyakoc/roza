<?php
/* @var $this OfferController */
/* @var $model Offer */
/* @var $form CActiveForm */

$criteria = new CDbCriteria();

echo CHtml::hiddenField('save_offer_attributes', true);

if ($model->eavAttributes) {
    $criteria->select = '`t`.*';
    $criteria->distinct = true;
    $criteria->join = 'LEFT JOIN `offer_eav` ON `offer_eav`.`attribute_id` = `t`.`id`';
    $criteria->order = 'name, `offer_eav`.`id` DESC, name';
} else {
    $criteria->order = 'name';
}

$eavAttributes = EavAttribute::model()->findAll($criteria);
?>

<div class="form">
    <div class="row-bootstrap">
        <?php foreach ($eavAttributes as $eavAttribute): ?>
            <?php /** @var $eavAttribute EavAttribute */ ?>
            <?php
            $value = $model->getEavAttributeValue($eavAttribute->id);
            ?>
            <div class="row col-md-4 form-group">
                <label class="<?= $value ? 'text-danger' : '' ?>" for="attribute-<?= $eavAttribute->id ?>"><?= $eavAttribute->name ?></label>
                <input id="attribute-<?= $eavAttribute->id ?>" type="text" class="form-control" name="OfferEav[<?= $eavAttribute->id ?>]" value="<?= $value ?>" placeholder="">
            </div>
        <?php endforeach; ?>
    </div>
</div>
