<?php
/**
 * Тип поля "Файл".
 *
 * @var \common\ext\dataAttribute\widgets\DataAttribute $this 
 * @var string $name имя поля
 * @var string $value значение поля
 * @var string|array $data данные типа
 * @var string $view шаблон отображения
 * @var array $params дополнительные параметры
 * @var boolean $isTemplate генерируется шаблон нового элемента.
 * 
 * В $params дожен быть передан параметр "url" - ссылка на 
 * действие загрузки файла. Действие может быть подключено 
 * к контроллеру \common\ext\file\actions\jQueryUploadFileAction
 * 
 * Могут быть также переданы параметры:
 * "options" (array) массив опций для плагина jQuery-File-Upload. По умолчанию не заданы.
 * "single" (boolean) использовать режим загрузки одного файла. По умолчанию TRUE.
 * "ui" (boolean) использовать режим jQuery-UI. По умолчанию FALSE.
 * "htmlOptions" (array) HTML атрибуты для DOM-элемента загрузки.
 * По умолчанию ['class'=>'form-control'].
 */
use common\components\helpers\HArray as A;

$single=A::get($params, 'single', true);

$htmlOptions=A::get($params, 'htmlOptions', ['class'=>'form-control']);
$htmlOptions['disabled']=$isTemplate;

if(empty($htmlOptions['id'])) {
	$htmlOptions['id']=uniqid('f');
}
$id=$htmlOptions['id'];

$params['options']['dataType']='json';
$params['options']['progressall']='js:function (e, data) {
	var progress = parseInt(data.loaded / data.total * 100, 10);
	$("#progress_'.$id.' .progress-bar").css("width", progress + "%");
}';
$params['options']['send']='js:function (e, data) { $("#progress_'.$id.'").show(); }';
$params['options']['done']='js:function (e, data) {
	console.log(data.result);
	setTimeout(function() {
		$("#progress_'.$id.'").hide(); 
		$("#progress_'.$id.' .progress-bar").css("width", "0%");
	}, 1500);
	if(data.result) {
		$("#list_'.$id.'").show();
		$.each(data.result.files_'.$id.', function (index, file) {
	   		var $item=$($("#list_item_'.$id.'").html());
			$item.find("img").attr("src", file.thumbnailUrl);
			$item.find("input").val(file.url);
	   		$item.appendTo("#list_'.$id.'");
		});
	}
}';
$params['options']['paramName']='files_'.$id.'[]';

?><span class="btn btn-success fileinput-button"><i class="glyphicon glyphicon-upload"></i> <span>Start upload</span><?
echo \CHtml::fileField('files_'.$id.'[]', '', $htmlOptions);
?></span>
<div id="progress_<?=$id?>" class="progress progress-striped" style="margin:5px 0;display:none">
	<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;height:20px;"></div>
</div><?

$this->owner->widget('\common\ext\file\widgets\JQueryUploadFile', [
	'selector'=>'#'.$htmlOptions['id'], 
	'url'=>A::get($params, 'url'), 
	'single'=>A::get($params, 'single', true),
	'options'=>A::get($params, 'options', []),
	'ui'=>A::get($params, 'ui', false)
]);
?><div id="list_item_<?=$id?>" style="display:none!important"><div class="col-xs-6 col-md-4">
	<input type="hidden" name="<?=$name.($single?'':'[]')?>" value="" />
	<img class="thumbnail" src=""/>
</div></div>
<div id="list_<?=$id?>" class="row" style="display:none"></div>
<? if($value):
	if(!is_array($value)) $data=[$value];
	else $data=$value; 
	?><div class="row"><?
	foreach($data as $val): if(!empty($val)):
	?><div class="col-xs-6 col-md-4"><?
		echo CHtml::hiddenField($name.($single?'':'[]'), $val);
		echo CHtml::image($val, '', ['class'=>'thumbnail', 'style'=>'max-width:80px;max-height:80px']); 
	?></div><?
	endif; endforeach;?></div><?
endif; ?>