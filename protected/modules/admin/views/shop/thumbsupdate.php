<?php
Yii::app()->clientScript->registerScriptFile(
    Yii::app()->baseUrl.'/js/jquery.jcrop.min.js'
);
?>
<?php
Yii::app()->clientScript->registerCssFile(
    Yii::app()->baseUrl.'/css/jcrop/jquery.jcrop.css'
);
?>
<?php $this->pageTitle = 'Редактирование эскизов - '. $this->appName; ?>
<script type="text/javascript">
var jcrop_api;  
var currentTmb;
$(function(){

	currentTmb = $('#mainImgTmb');

	var tmbX = 0;
	var tmbY = 0;
	var tmbH = 0;
	var tmbW = 0;
	

	initJcrop();
    function initJcrop() {
        jcrop_api = $.Jcrop('#bigImg');
    };
	
	jcrop_api.setImage($('#bigImg').attr('src')); 

	$('#bigImg').Jcrop({
		onChange: function(coords){showPreview(coords);},
		onSelect: function(coords){showPreview(coords);},
		onRelease: function(){hidePreview();},
		aspectRatio: 1
	});

	
	var $preview = $('#preview');

	function showPreview(coords) {
	
		tmbX = coords.x;
		tmbY = coords.y;
		tmbH = coords.h;
		tmbW = coords.w;

		var w = $('#bigImg').data('w');
		var h = $('#bigImg').data('h');

		if (parseInt(coords.w) > 0) {
		  var rx = 200 / coords.w;
		  var ry = 200 / coords.h;

		  $preview.css({
		    width: Math.round(rx * w) + 'px',
		    height: Math.round(ry * h) + 'px',
		    marginLeft: '-' + Math.round(rx * coords.x) + 'px',
		    marginTop: '-' + Math.round(ry * coords.y) + 'px'
		  }).show();
		}
	}

	function hidePreview() {
		$preview.stop().fadeOut('fast');
	}

	$("#onlyImages").on( "click", "img", function(){ setImage( $(this) ); } );
	$("#mainImgTmb").on( "click", 		 function(){ setImage( $(this) ); } );

	function setImage(self) {
		var t = self;
		currentTmb = t;
		$('jcrop-holder').find('img').fadeOut('fast');
		jcrop_api.setImage($(t).data('raw'));
		$('#bigImg').data('w', $(t).data('w'))
					.data('h',$(t).data('h'))
					.attr('src', $(t).data('raw'));
		$('#preview').attr('src', $(t).data('raw'))
					 .data('tmb', $(t).attr('src'));
	}

	$("#applyResize").on('click', function(){
		$.ajax({
			url: '/admin/shop/resize',
			type: 'post',
			async: false,
			data: { x: tmbX, y: tmbY, h: tmbH, w: tmbW, src: $("#bigImg").attr('src'), dst: $("#preview").data('tmb') },
			success: function (data) {
				$("#applyResize").val('Сохранено');
				setTimeout(function(){$("#applyResize").val('Применить');}, 1000);
				$(currentTmb).removeAttr('src').attr('src', $("#preview").data('tmb') + "?" + Math.random());
			}
		});
	});
});

</script>

<style type="text/css">
.jcrop-holder {
	display:inline-block;
	vertical-align: top;
	margin-bottom:5px;
}
.js-link {
	display:none;
}
</style>
<h1>Редактирование эскизов</h1>
<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'product-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ),
        'htmlOptions'=>array('enctype'=>'multipart/form-data'),
    )); ?>

    <div class="row">
        <?php if ($mainImg = $model->getBigMainImg(true)): ?>
            <div id="mainImg" class="mainImg modelImages">
                <div class="img">
                    <img src="<?php echo $mainImg; ?>" alt="" id="bigImg" data-w="<?php echo $model->getWidth($mainImg); ?>" data-h="<?php echo $model->getHeight($mainImg); ?>" style="display:none;"/>
                    <div style="width:200px;height:200px;overflow:hidden;margin:0 5px 5px 5px; display:inline-block;">
						<img src="<?php echo $mainImg; ?>" id="preview" data-tmb="<?php echo $model->getMainImg(true); ?>"/>
					</div>
                </div>
            </div>
        <?php endif; ?>
		<input type="button" id="applyResize" value="Применить" class="default-button"/>
    </div>
    
    <div class="row">
        <div class="non-required <?php if (!count($model->moreImages)) echo ' hidden'; ?>">
        	<img src="<?php echo $model->getMainImg(true); ?>" alt=""  id="mainImgTmb" data-raw="<?php echo $mainImg; ?>" data-w="<?php echo $model->getWidth($mainImg); ?>" data-h="<?php echo $model->getHeight($mainImg); ?>"/>
            <?php $this->widget('widget.adminImages.adminImages', array('model'=>$model, 'viewImages'=>'onlyimages')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div><!-- form -->