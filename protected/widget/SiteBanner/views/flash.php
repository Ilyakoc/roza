<?php
/**
 * File: flash.php / 05.06.13, 12:08
 * @author Mobyman
 */
$width = isset(Yii::app()->params['banner']['slide']['width']) ? Yii::app()->params['banner']['slide']['width'] : 240;
$height = isset(Yii::app()->params['banner']['slide']['width']) ? Yii::app()->params['banner']['slide']['width'] : 320;
?>
<div id="banner" style="margin: 0 auto; position: relative; width: <?php echo $width; ?>px"></div>
<div class="clr"></div>
<div style="height:20px"></div>
<script type="text/javascript">
	$(function(){
		$('#banner').flash({
			src: <?php printf("'%s'", $banners[0]->src); ?>,
			width: <?php echo $width; ?>,
			height: <?php echo $height; ?>,
			flashvars: { link1: <?php printf("'%s'", $banners[0]->link); ?> }
		});
	});
</script>