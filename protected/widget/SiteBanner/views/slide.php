<div class="banners-wrapper">
<div class="banner-descript">
	<span id="bannerpag"></span>
</div>
<div class="bannershow" style="display:none;">
 	<?php $i = 1; foreach($banners as $banner): ?>
 		<div>
 	 		<a href="<?php echo $banner->link; ?>">
 	 			<img src="<?php echo $banner->src; ?>" alt="" />
			    <span class="comment"><?php echo $banner->title; ?></span>
 	 		</a>
 	 	</div>
   <?php $i++; endforeach; ?>
</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		if($('.bannershow').children().length != 1) {
		    $('.bannershow').cycle({
				fx: 'fade',
				pager: '#bannerpag'
			});
			$('.bannershow').show();
		} else {
			$('.banner-descript').hide();
			$('.bannershow').show().children().first().addClass('first-slide');

		}
	});
</script>

<style type="text/css">
	.banners-wrapper {
		<?php printf("height:%dpx;", Yii::app()->params['banner']['carousel']['height'] + 180); ?>
	}
	.comment {
		margin: 0 auto;
		width: 240px;
		position: absolute;
		display: block;
		top: 210px;
		color: white;
		background: rgba(40, 40, 40, 0.5);
		height: 30px;
		text-indent: 8px;
		vertical-align: middle;
		line-height: 2.1;
	}
	.first-slide {
		height: 0px;
	}
	.banner-descript {
		<?php printf("width:%dpx;", Yii::app()->params['banner']['carousel']['width'] + 10); ?>
		<?php printf("top:%dpx;", Yii::app()->params['banner']['carousel']['height'] + 208); ?>
		height: 30px;
		display:block;
		position:absolute;
		color: white;
		font-size:18px;
		z-index:10;
		margin: 0 auto;
	}
	#bannerpag {
		margin: 0 auto;
		width: 240px;
		position: relative;
		text-align: center;
		display: block;
		margin-left: 35%;
	}
	#bannerpag a {
		text-indent: -9999px;
		display: inline-block;
		background: #EBEBEB;
		width: 6px;
		height: 6px;
		margin:2px 3px;
		-webkit-border-radius: 50%;
		-moz-border-radius: 50%;
		border-radius: 50%;
		outline: none;
	}
	#bannerpag a.activeSlide {
		background: #00FF00;
	}
	<?php if(isset(Yii::app()->params['banner']) && Yii::app()->params['banner']['showCaption']): ?>
	<?php endif; ?>
</style>
