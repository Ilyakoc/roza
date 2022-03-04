<?
/* @var AdminController $this */
/* @var CClientScript $cs */
use common\components\helpers\HArray as A;
use settings\components\helpers\HSettings;

?>
<!DOCTYPE html>
<html>
<head>
<?php 
    $shopMenuItems = [];
	$shopMenuItems = A::m($shopMenuItems, HSettings::getMenuItems($this, 'rangeof', 'settings/index'));
?>