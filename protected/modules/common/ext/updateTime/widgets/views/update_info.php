<?php
/** @var \common\ext\updateTime\widgets\UpdatedInfo $this */
use common\components\helpers\HYii as Y;

echo CHtml::tag($this->tag, $this->htmlOptions, Y::formatDateVsRusMonth($this->getDateTime()));
?>