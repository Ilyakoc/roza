<?php
/** @var \СController $this */
/** @var string $cid индетификатор настроек CRUD для модели */
/** @var \CActiveForm $form объект формы */
/** @var \CActiveRecord $model модель */
/** @var array $attributes массив настроек атрибутов модели */
use crud\components\helpers\HCrudForm;

echo HCrudForm::getHtmlFields($cid, $attributes, $model, $form, $this);
?>