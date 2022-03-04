Расширение: common\ext\file
Подключение и использование
---------------------------
(!) Для версии младше (<=2.1) необходимо подключить компонент \CImageHandler (/protected/components) в /protected/config/defaults.php
    'ih'=>array(
       	'class'=>'CImageHandler',
	),
    
(!) Также иногда директория изображений создается с установленым sticky bit. При этом файлы из такой директории удалялся не будут. 
Нужно вручную переустановить права на папку.
    
1) В модели подключить поведение.

(!) Для моделей \CActiveRecord, класс модели должен быть наследован либо от \DActiveRecord, либо (>=2.2.1) от \common\components\base\ActiveRecord.
(!) Для моделей \CFormModel, класс необходимо наследовать от \common\components\base\FormModel и вызывать в методе сохранения $this->save(false).
При использовании \common\components\base\FormModel метод сохранения должен назваться update($attributes).

    параметр "imageMode"=>true для изображений
    (!) Для \СFormModel добавить соответсвующие атрибуты (attirbute, attributeAlt, attributeEnable) как "public $attirbute".
    
    параметр "types"=>"ext1,ext2" для файлов по умолчанию "doc,xsl,pdf,docx,xslx,txt", для изображений "jpg,jpeg,png,gif"
    
    параметры "attributeEnable" и "attributeAlt" не являются обязательными.
    
    use common\components\helpers\HArray as A;
    
    public function behaviors()
    {
        return A::m(parent::behaviors(), [
            ...
        	'imageBehavior'=>[
    			'class'=>'\common\ext\file\behaviors\FileBehavior',
    			'attribute'=>'image',
    			'attributeLabel'=>'Изображение',
    			'attributeEnable'=>'image_enable',
    			'attributeAlt'=>'image_alt',
    			'imageMode'=>true 
    		],
            'fileBehavior'=>[
    			'class'=>'\common\ext\file\behaviors\FileBehavior',
    			'attribute'=>'file',
    			'attributeLabel'=>'Файл',
    			'attributeEnable'=>'file_enable',
    			'attributeAlt'=>'file_alt',
    		]
        ]);
    }

2) В контроллере формы подключить действие.

    use common\components\helpers\HArray as A;
    
    public function actions()
	{
		return A::m(parent::actions(), [
			'removeImage'=>[
				'class'=>'\common\ext\file\actions\RemoveFileAction',
				'modelName'=>'\MyModel',
				'behaviorName'=>'imageBehavior',
				'ajaxMode'=>true
			],
            'removeFile'=>[
				'class'=>'\common\ext\file\actions\RemoveFileAction',
				'modelName'=>'\MyModel',
				'behaviorName'=>'fileBehavior',
				'ajaxMode'=>true
			]
		]);
	}
    
    (!) При включеном режиме ajaxMode=>TRUE нужно добавить действие в фильтр:
    public function filters()
	{
		return A::m(parent::filters(), [
			'ajaxOnly + removeImage, removeFile'
		]);
	}
    
3) В форме редактирования подключить виджет.

Для формы установить тип "enctype"=>"multipart/form-data"
Для \CActiveForm:
    'htmlOptions'=>['enctype'=>'multipart/form-data'],
____________________________________________________
ВАЖНО! Для \CActiveRecord виджет обернуть в условие: 
if(!$model->isNewRecord): $this->widget(...); endif; 
----------------------------------------------------

3.1) для изображения
Параметры "tmbWidth" и "tmbHeight" задают размер миниатюры (не обязательны, по умолчанию 150x150).

<? $this->widget('\common\ext\file\widgets\UploadFile', [
	'behavior'=>$model->imageBehavior, 
	'form'=>$form, 
	'actionDelete'=>$this->createAction('removeImage'),
    'tmbWidth'=>200,
    'tmbHeight'=>200,
    'view'=>'panel_upload_image'
]); ?>

3.2) для файла
<? $this->widget('\common\ext\file\widgets\UploadFile', [
	'behavior'=>$model->fileBehavior, 
	'form'=>$form, 
	'actionDelete'=>$this->createAction('removeFile'),
    'view'=>'panel_upload_file'
]); ?>

4) Использование в шаблонах публичной части.
параметр $absolute определяет формировать абсолютную ссылку или нет.
параметр $schema протокол (http, https).

4.1) вывод изображения в теге <img>
при заданных $width и $height будет сгенерирован файл миниатюры.
$model->img($width=false, $height=false, $proportional=true, $htmlOptions=[], $absolute=false, $schema='')// вывод изображения в теге <img>

4.2) вывод ссылки на файл <a>
$model->link($htmlOptions=[], $absolute=false, $schema='')

4.3) вывод ссылки на файл с программным скачиванем <a>
$downloadUrl ссылка на действие скачивания
$model->downloadLink($htmlOptions=[], $absolute=false, $schema='', $downloadUrl='/download')

Для версий ниже (<=2.2.1)
4.3.1) Подключить действие в контроллер:
    use common\components\helpers\HArray as A;
    
	public function actions()
	{
		return A::m(parent::actions(), [
            ...
			'downloadFile'=>[
				'class'=>'\common\ext\file\actions\DownloadFileAction',
				'allowDirs'=>['files']
			]
		]);
	}

4.3.2) Добавить правило в UrlManager. 
    '/download/<filename:.*>'=>'mycontroller/downloadFile',
    
4.4) Получить ссылку $model->getSrc($absolute=false, $schema='')
4.5) Получить ссылку на миниатюру $model->getTmbSrc($width=false, $height=false, $proportional=true, $absolute=false, $schema='')
