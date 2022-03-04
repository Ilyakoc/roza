<?php
/**
 * Поведение "атрибут файл" для модели \common\components\base\ActiveRecord.
 *
 * Если используется модель формы \CFormModel, то модель необходимо 
 * наследовать от \common\components\base\FormModel 
 */
namespace common\ext\file\behaviors;

use common\components\helpers\HYii as Y;
use common\components\helpers\HDb;
use common\components\helpers\HHash;
use common\components\helpers\HHtml;

class FileBehavior extends \CBehavior
{
	/**
	 * @var string имя атрибута id модели. Может быть передано пустое 
	 * значение, в этом случае, файл будет привязан к основному атрибуту.
	 * Для моделей наследуемых не от \common\components\base\FormModel 
	 * по умолчанию будет установлено "id". 
	 */
	public $attributeId=false;
	
	public $maxWidth = 2000;
	public $maxHeight = 2000;

	/**
	 * @var string имя атрибута имени файла. (основной атрибут)
	 */
	public $attribute;
	
	/**
	 * @var string название поля формы для атрибута имени файла.  
	 * По умолчанию (FALSE) будет отображено заданное в поведении. 
	 */
	public $attributeLabel=false;
	
	/**
	 * @var string имя атрибута публикации файла. 
	 * По умолчанию (FALSE) не задан. 
	 */
	public $attributeEnable=false;
	
	/**
	 * @var string название поля формы для атрибута публикации подписи к файлу.
	 * По умолчанию (FALSE) будет отображено заданное в поведении. 
	 */
	public $attributeEnableLabel=false;
	
	/**
	 * @var boolean значение атрибута публикации файла по умолчанию. По умолчанию (FALSE) не опубликован. 
	 * При FileBehavior::$attributeEnable=FALSE значение будет установлено в TRUE (опубликован).
	 */
	public $enableValue=false;
	
	/**
	 * @var string имя атрибута HTML-атрибута ALT (для изображений) и TITLE (для изображений и файлов). 
	 * По умолчанию (FALSE) HTML-атрибут не задан. 
	 */
	public $attributeAlt=false;
	/**
	 * @var string название поля формы для атрибута HTML-атрибута ALT/TITLE.
	 * По умолчанию (FALSE) будет отображено заданное в поведении. 
	 */
	public $attributeAltLabel=false;
	
	/**
	 * @var string|false имя атрибута для значения по умолчанию HTML-атрибута ALT/TITLE.
	 */
	public $attributeAltEmpty=false;
	
	/**
	 * @var string имя атрибута файла для формы загрузки. 
	 * По умолчанию (FALSE) будет сгенерирован автоматически.
	 */
	public $attributeFile=false;
	
	/**
	 * @var string название поля формы для атрибута файла формы загрузки. 
	 * По умолчанию (FALSE) будет отображено заданное в поведении. 
	 */
	public $attributeFileLabel=false;	
	
	/**
	 * @var string|array|boolean URL изображения по умолчанию.
	 * Может быть передан массив параметров для HHtml::phSrc()
	 * Может быть передано значение true, тогда изображение будет 
	 * генерироваться автоматически.
	 */
	public $defaultSrc=true;
	
	/**
	 * @var string путь к файлам поведения (относительно алиаса "webroot").
	 * По умолчанию (FALSE) будет сгенерирован автоматически.
	 */
	public $filePath=false;
	
	/**
	 * @var string базовый путь к файлам поведения (относительно алиаса "webroot"). 
	 * По умолчанию (FALSE) "files" для режима FileBehavior::$imageMode=FALSE, 
	 * "images" при режиме FileBehavior::$imageMode=TRUE.
	 * Не будет учтен, если задан параметр FileBehavior::$filePath   
	 */
	public $baseFilePath=false;
	
	/**
	 * @var boolean только изображения.
	 */
	public $imageMode=false;
	
	/**
	 * @var string разрешенные расширения файлов, через запятую (напр. "jpg,png")
	 * По умолчанию (FALSE) будут установлены заданные в поведении.
	 */
	public $types=false;
	
	/**
	 * @var integer максимальный размер загружаемого файла.
	 * По умолчанию 10Мб.
	 */
	public $maxSize=10485760;
	
	/**
	 * @var string префикс для имен файлов.
	 * По умолчанию (FALSE) не задан.
	 */
	public $prefix=false;
	
	/**
	 * @var boolean режим множественной загрузки файлов.
	 * По умолчанию FALSE.
	 * @todo на данный момент, работает только с первым файлом из списка.
	 */
	public $multiFiles=false;
	
	/**
	 * @var boolean принудительно производить попытку 
	 * получения имени файла.
	 */
	public $forcyGetFile=false;

    /**
     * @var bool
     * используется для инфоблоков. выставляется в true, если используется для дополнительных свойств инфоблока
     */
    public $forProperty = false;
	
	/**
	 * @var string разрешеные расширения для режима FileBehavior::$imageMode=TRUE
	 */
	protected $defaultImageTypes='jpg,jpeg,png,gif';
	
	/**
	 * @var string разрешеные расширения для основного режима (FileBehavior::$imageMode=FALSE)
	 */
	protected $defaultFileTypes='doc,xsl,pdf,docx,xslx,txt';
	
	/**
	 * @var string префикс для файлов превью-изображений.
	 */
	protected $tmbPrefix='tmb';
	
	/**
	 * (non-PHPdoc)
	 * @see \CBehavior::events()
	 */
	public function events()
	{
		return array(
			'onBeforeSave'=>'beforeSave',
			'onAfterSave'=>'afterSave',
			'onAfterDelete'=>'afterDelete'
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CBehavior::attach()
	 */
	public function attach($owner)
	{
		parent::attach($owner);
		
		if(empty($this->attributeFile)) {
			$this->attributeFile=$this->attribute . '_file';
		}
		
		$owner->addDynamicAttribute($this->attributeFile);
		
		if(!$this->hasEnabled()) {
			$this->enableValue=true;
		}
		
		if($this->baseFilePath === false) {
			if($this->imageMode) {
				$this->baseFilePath='images';
			}
			else {
				$this->baseFilePath='files';
			}
		} 
		else {
			$this->baseFilePath=trim($this->baseFilePath, '/\\');
		}
		
		if($this->filePath === false) {
			$this->filePath=$this->baseFilePath . Y::DS . $this->resolveOwner();
		}
		$this->filePath=trim($this->filePath, '/\\');
		
		$t=Y::ct(__CLASS__.'.file', 'common');
		if($this->attributeLabel === false)
			$this->attributeLabel=$t('label.attribute');
		if($this->attributeEnableLabel === false)
			$this->attributeEnableLabel=$t('label.attributeEnable');
		if($this->attributeFileLabel === false)
			$this->attributeFileLabel=$t('label.attribute');
		if($this->attributeAltLabel === false)
			$this->attributeAltLabel=$t('label.attributeAlt');
		
		if($this->owner instanceof \common\components\base\FormModel) {
			if(!$owner->hasProperty($this->attribute)) {
				$owner->addDynamicAttribute($this->attribute);
			}
			if($this->hasAlt() && !$owner->hasProperty($this->attributeAlt)) {
				$owner->addDynamicAttribute($this->attributeAlt);
			}
			if($this->hasEnabled() && !$owner->hasProperty($this->attributeEnable)) {
				$owner->addDynamicAttribute($this->attributeEnable);
			}
		}
		elseif(empty($this->attributeId)) {
			$this->attributeId='id';
		}
		
		if($this->forcyGetFile && !$owner->{$this->attribute}) {
			$this->owner->{$this->attribute}=$this->getAttributeValueForcy();
		}

		// поддержка модуля Регионов
		if(Y::module('extend.regions')) {
            if($this->prefix) {
                $this->prefix=\extend\modules\regions\components\helpers\HRegion::i()->getPostfix() . '_' . $this->prefix;
            }
            else {
                $this->prefix=\extend\modules\regions\components\helpers\HRegion::i()->getPostfix();
            }
        }
	}
	
	/**
	 * (non-PHPDoc)
	 * @see \CModel::rules()
	 */
	public function rules()
	{
		$rules=[
			[$this->attribute, 'length', 'max'=>64],
			[$this->attribute, 'safe']
		];
		if($this->hasEnabled()) {
			$rules[]=[$this->attributeEnable, 'boolean'];
			$rules[]=[$this->attributeEnable, 'safe'];
		}
		if($this->hasAlt()) {
			$rules[]=[$this->attributeAlt, 'length', 'max'=>255];
			$rules[]=[$this->attributeAlt, 'safe'];
		}
		
		if($this->types) $types=$this->types;
		elseif($this->imageMode) $types=$this->defaultImageTypes;
		else $types=$this->defaultFileTypes;
		
		if($this->imageMode) {
			$rules[]=[$this->attributeFile, 'DImageValidator', 'allowEmpty'=>true, 'types'=>$types, 'maxSize'=>$this->maxSize];
			$rules[]=[$this->attributeFile, 'EImageValidator', 'width' => $this->maxWidth, 'height' => $this->maxHeight, 'allowEmpty'=>true];
		}
		else {
			$rules[]=[$this->attributeFile, 'file', 'allowEmpty'=>true, 'types'=>$types, 'maxSize'=>$this->maxSize];
		}
		
		return $rules;
	}
	
	/**
	 * (non-PHPDoc)
	 * @see \CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return [
			$this->attribute=>$this->attributeLabel,
			$this->attributeEnable=>$this->attributeEnableLabel,
			$this->attributeAlt=>$this->attributeAltLabel,
			$this->attributeFile=>$this->attributeFileLabel
		];
	}
	
	/**
	 * Проверка существования файла.
	 * @return boolean
	 */
	public function exists()
	{
		return (bool)is_file($this->getFilename(true));
	}
	
	/**
	 * Получить id внешней модели.
	 * @return mixed
	 */
	public function getId()
	{
		if(!empty($this->attributeId)) 
			return $this->owner->{$this->attributeId};
		
		return null; 
	}
	
	/**
	 * Используется ли атрибут HTML-атрибута ALT/TITLE или нет?
	 * @return boolean
	 */
	public function hasAlt()
	{
		return (bool)$this->attributeAlt;
	}
	
	/**
	 * Получить значение атрибута HTML-атрибута ALT/TITLE.
	 * @return mixed
	 */
	public function getAlt()
	{
		$alt='';
		
		if($this->hasAlt()) {
			$alt=$this->owner->{$this->attributeAlt};
		}
		
		if(!$alt && $this->attributeAltEmpty) {
			$alt=$this->owner->{$this->attributeAltEmpty};
		}
		
		return $alt;
	}
	
	/**
	 * Используется ли атрибут публикации или нет?
	 * @return boolean
	 */
	public function hasEnabled()
	{
		return (bool)$this->attributeEnable;
	}
	
	/**
	 * Файл опубликован на сайте.
	 * @return boolean
	 */
	public function isEnabled()
	{
		return ($this->hasEnabled() && (bool)$this->owner->{$this->attributeEnable});
	}
	
	
	/**
	 * Получить полный путь к директории файла.
	 * @return string
	 */
	public function getPath()
	{
		return \Yii::getPathOfAlias('webroot') . Y::DS . $this->filePath;
	}
	
	/**
	 * Получить ссылку на файл
	 * @param boolean $absolute возвратить абсолютный путь. 
	 * По умолчанию (FALSE) возвращается относительный путь.
	 * @param string $schema schema to use (e.g. http, https). If empty, the schema used for the current request will be used.
	 * Требуется при создании абсолютного пути.
	 * @return Ambigous <string, NULL> 
	 * Если файла не существует, будет возвращено NULL.
	 */
	public function getSrc($absolute=false, $schema='')
	{
		if(is_file($this->getFilename(true))) {
			$src=preg_replace('#[/\\\\]+#', '/', $this->filePath) . '/' . $this->getFilename();
			if($absolute) {
				return \Yii::app()->createAbsoluteUrl($src, [], $schema);
			}
			return '/' . $src;
		}
		return null;
	}
	
	/**
	 * 
	 * @param string $width fit to width. Can be false, which means that this parameter is ignored
	 * @param string $height fit to height. Can be false, which means that this parameter is ignored
	 * @param boolean $proportional  proportional scaling. Default value is true
	 * @param boolean $absolute возвратить абсолютный путь. По умолчанию (FALSE) возвращает относительный путь.
	 * @param string $schema schema to use (e.g. http, https). If empty, the schema used for the current request will be used.
	 * Требуется при создании абсолютного пути.
	 * @param boolean $adaptive возвратить адаптивное изображение.
	 * @param string|array|false $default изображение по умолчанию. 
	 * Может быть передан массив с параметрами для метода HHtml::phSrc()
	 * @return Ambigous <string, NULL> 
	 * Если файла не существует, будет возвращено NULL.
	 */
	public function getTmbSrc($width=false, $height=false, $proportional=true, $absolute=false, $schema='', $adaptive=false, $default=false)
	{
		if(is_file($this->getFilename(true))) {
			if(!is_numeric($width)) $width=false;
			if(!is_numeric($height)) $height=false;
			
			$tmbFilename=$this->tmbPrefix . '_' . (($width === false) ? 'n' : $width) . 'x' . (($height === false) ? 'n' : $height) . '_' . $this->getFilename();
			if(!is_file($this->getPath() . Y::DS . $tmbFilename) || YII_DEBUG) {
				$image=\Yii::app()->ih->load($this->getFilename(true));
				if($adaptive) {
					$image=$image->adaptiveThumb($width, $height);
				}
				else {
					$image=$image->thumb($width, $height, $proportional);
				}
				$image->save($this->getPath() . Y::DS . $tmbFilename);
			}
			
			$src=preg_replace('#[/\\\\]+#', '/', $this->filePath) . '/' . $tmbFilename;
			if($absolute) {
				return \Yii::app()->createAbsoluteUrl($src, [], $schema);
			}
			return '/' . $src;
		}
		
		$t=Y::ct(__CLASS__.'.file', 'common');
		
		return $this->getDefaultSrc($default, [
			'w'=>$width, 
			'h'=>$height, 
			'bg'=>'ffffff', 
			'c'=>'a0a0a0', 
			't'=>$t('defaultSrc.text'),
			'sz'=>36
		]);
	}
	
	/**
	 * Получить html-код тэга <img>
	 */
	public function img($width=false, $height=false, $proportional=true, $htmlOptions=[], $absolute=false, $schema='', $adaptive=false, $addTime=false)
	{
		if(empty($htmlOptions['title'])) 
			$htmlOptions['title']=$this->getAlt();
		 
		return \CHtml::image($this->getTmbSrc($width, $height, $proportional, $absolute, $schema, $adaptive) . ($addTime?'?'.time():''), $this->getAlt(), $htmlOptions);
	}
	
	/**
	 * Получить ссылку на изображение по умолчанию
	 * @param string|array|false $default изображение по умолчанию. 
	 * Может быть передан массив с параметрами для метода HHtml::phSrc()
	 * Если передано false, будет получено из параметра FileBehavior::$defaultSrc
	 * @param array $options массив дополнительных параметров для HHtml::phSrc()  
	 * @return string
	 */
	public function getDefaultSrc($default=false, $options=[])
	{
		if($default) {
			if($default === true) {
				return HHtml::phSrc($options);
			}
			elseif(is_array($default)) {
				return HHtml::phSrc(A::m($default, $options));
			}
			else {
				return $default;
			}
		}
		elseif($this->defaultSrc) {
			return $this->getDefaultSrc($this->defaultSrc, $options);
		}
		
		return '';
	}
	
	/**
	 * Получить html-код тэга <a>
	 */
	public function link($htmlOptions=[], $absolute=false, $schema='')
	{
		if($this->getAlt()) $title=$this->getAlt();
		else $title=$this->getFilename();
		
		if(empty($htmlOptions['title'])) {
			$htmlOptions['title']=$this->getAlt();
		}
		
		return \CHtml::link($title, $this->getSrc($absolute, $schema), $htmlOptions);
	}
	
	public function downloadLink($htmlOptions=[], $absolute=false, $schema='', $downloadUrl='/download')
	{
		if(empty($htmlOptions['title']))
			$htmlOptions['title']=$this->getAlt();
		
		$url=$downloadUrl . $this->getSrc();
		if($absolute) {
			$url=\Yii::app()->createAbsoluteUrl($url, [], $schema);
		}
		
		return \CHtml::link($this->getFilename(), $url, $htmlOptions);
	}
	
	/**
	 * Получить имя файла.
	 * @param boolean $absolute возвращать полный путь к файлу.
	 * @return  Ambigous <string, NULL> 
	 * Возвращает NULL если имя файла не задано.
	 */
	public function getFilename($absolute=false)
	{
		if($this->owner->{$this->attribute}) {			
			if($absolute) {
				return $this->getPath() . Y::DS . $this->owner->{$this->attribute};
			}
			return $this->owner->{$this->attribute};
		}
		return null;
	}
	
	/**
	 * Получить базовое имя файла (без расширения).
	 * @param boolean $absolute возвращать, влючая полный путь к файлу.
	 * @return string 
	 */
	public function getBasename($absolute=false)
	{
		if(empty($this->attributeId)) {
			$id=$this->attribute;
		}
		else { 
			$id=$this->owner->{$this->attributeId};
		}
		$basename=$this->prefix . $id . '_' . HHash::get($this->resolveOwner() . $this->attribute . $id, 12);
		
		if($absolute) {
			return $this->getPath() . Y::DS . $basename;
		}
		return $basename;
	}
	
	/**
	 * Получить принудительно имя файла.
	 * @return string|NULL
	 */
	public function getAttributeValueForcy()
	{
		$files=glob($this->getBasename(true) . '.*');
		if(is_array($files)) {
			return basename(array_shift($files));
		}
		
		return null;
	}

	protected function autorotate(\Imagick $image)
	{
	    switch ($image->getImageOrientation()) {
	    case \Imagick::ORIENTATION_TOPLEFT:
	        break;
	    case \Imagick::ORIENTATION_TOPRIGHT:
	        $image->flopImage();
	        break;
	    case \Imagick::ORIENTATION_BOTTOMRIGHT:
	        $image->rotateImage("#000", 180);
	        break;
	    case \Imagick::ORIENTATION_BOTTOMLEFT:
	        $image->flopImage();
	        $image->rotateImage("#000", 180);
	        break;
	    case \Imagick::ORIENTATION_LEFTTOP:
	        $image->flopImage();
	        $image->rotateImage("#000", -90);
	        break;
	    case \Imagick::ORIENTATION_RIGHTTOP:
	        $image->rotateImage("#000", 90);
	        break;
	    case \Imagick::ORIENTATION_RIGHTBOTTOM:
	        $image->flopImage();
	        $image->rotateImage("#000", 90);
	        break;
	    case \Imagick::ORIENTATION_LEFTBOTTOM:
	        $image->rotateImage("#000", -90);
	        break;
	    default: // Invalid orientation
	        break;
	    }
	    $image->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
	    return $image;
	}
	
	/**
	 * Event: onBeforeSave.
	 * @return boolean
	 */
	public function beforeSave()
	{
		$file=null;
		if($this->multiFiles) {
			if($files=\CUploadedFile::getInstances($this->owner, $this->attributeFile)) {
				$file=$files[0];
			}
		}
		else {
			$file=\CUploadedFile::getInstance($this->owner, $this->attributeFile);
		}
		
		$isNewRecord=true;
		if($this->owner instanceof \CActiveRecord) {
			$isNewRecord=$this->owner->isNewRecord;  
		}
		
		if(!$isNewRecord && !empty($file)) {
			$this->delete();

			$this->owner->{$this->attribute}=$this->getBasename() . '.' . $file->getExtensionName();
			
            if($this->hasEnabled()) {
                if($this->enableValue) $this->owner->{$this->attributeEnable}=1;
                else $this->owner->{$this->attributeEnable}=0;
            }
			
			if(!is_dir($this->getPath()))
				mkdir($this->getPath(), '0755', true);
			
			$file->saveAs($this->getFilename(true));
			
			if(class_exists('\Imagick')) {
				$img = new \Imagick(realpath($this->getFilename(true)));

				$img = $this->autorotate($img);
				$img->stripImage(); // if you want to get rid of all EXIF data
				$img->writeImage();
			}
		}
		
		return true;
	}
	
	/**
	 * Event: onAfterSave.
	 * @return boolean
	 */
	public function afterSave()
	{
		$file=null;
		if($this->multiFiles) {
			if($files=\CUploadedFile::getInstances($this->owner, $this->attributeFile)) {
				$file=$files[0];
			}
		}
		else {
			$file=\CUploadedFile::getInstance($this->owner, $this->attributeFile);
		}

		$isNewRecord=true;
		if($this->owner instanceof \CActiveRecord) {
			$isNewRecord=$this->owner->isNewRecord;
		}
		
		if($isNewRecord && !empty($file)) {
			$this->delete(false);
			$this->owner->{$this->attribute}=$this->getBasename() . '.' . $file->getExtensionName();
            
            if($this->hasEnabled()) {
                if($this->enableValue) $this->owner->{$this->attributeEnable}=1;
                else $this->owner->{$this->attributeEnable}=0;
            }
			
			if($this->owner instanceof \CActiveRecord) {
				$cId=HDb::qc($this->attributeId);
				$cFile=HDb::qc($this->attribute);
                $params=[
                    ':id'=>$this->owner->{$this->attributeId},
					':file'=>$this->owner->{$this->attribute}
                ];
                
                if($this->hasEnabled()) {
                    $cEnable=HDb::qc($this->attributeEnable);
                    $sqlEnable=", {$cEnable}=:enable";
                    $params[':enable']=$this->owner->{$this->attributeEnable};
                }
                else $sqlEnable='';
                if (!$this->forProperty) {
                    $query = "UPDATE " . HDb::qt($this->owner->tableName()) . " SET {$cFile}=:file{$sqlEnable} WHERE {$cId}=:id";
                    $id = HDb::execute($query, $params);
                }
			}

			if(!is_dir($this->getPath())) 
				mkdir($this->getPath(), '0755', true);

			$file->saveAs($this->getFilename(true));
			
			if(class_exists('\Imagick')) {
				$img = new \Imagick(realpath($this->getFilename(true)));

				$img = $this->autorotate($img);
				$img->stripImage(); // if you want to get rid of all EXIF data
				$img->writeImage();
			}
		}
		
		return true;
	}
	
	/**
	 * Event: onAfterDelete
	 * @return boolean
	 */
	public function afterDelete()
	{
		$this->delete(true);
		
		return true;
	}
	
	/**
	 * Удалить файлы превью-изображений.
	 */
	public function deleteTrumbs()
	{
		$files=glob($this->getPath() . Y::DS . $this->tmbPrefix . '_*_' . $this->getBasename() . '.*');
		if(is_array($files)) {
			array_map('unlink', $files);
		}
	}
	
	/**
	 * Удаление файла
	 * @param boolean $ownerSave сохранить изменения во внешней модели.
	 * По умолчанию (TRUE) сохранять.
	 */
	public function delete($ownerSave=true)
	{
		$this->deleteTrumbs();
		
		$files=glob($this->getBasename(true) . '.*');
		if(is_array($files)) {
			array_map('unlink', $files);
		}
		 
		if($ownerSave) {
			if($this->owner instanceof \CActiveRecord) {
				$sqlUpdate=HDb::qc($this->attribute) . "=''";
				if(!empty($this->attributeEnable)) {
					$sqlUpdate.=', '.HDb::qc($this->attributeEnable) . '=' . ($this->enableValue ? '1' : '0');
				}
				if(!empty($this->attributeAlt)) {
					$sqlUpdate.=', '.HDb::qc($this->attributeAlt) . "=''";
				}
				
				$cId=HDb::qc($this->attributeId);
				if ($this->forProperty === false) {
                    $query = "UPDATE " . HDb::qt($this->owner->tableName()) . " SET {$sqlUpdate} WHERE {$cId}=:id";

                    $id=HDb::execute($query, [':id'=>$this->owner->{$this->attributeId}]);
                } else {
				    //todo:: сделать обновление соответствующего свойства инфоблока
                }
			}
			else {
				$this->owner->{$this->attribute}='';
				if($this->owner instanceof \common\components\base\FormModel) {
					$this->owner->update([$this->attribute=>'']);
				}
			}
		} 
		
		return true;
	}
	
	/**
	 * Разрешить имя класса внешней модели в имя категории хранения файлов поведения.
	 * @return string
	 */
	private function resolveOwner()
	{
		return strtolower(trim(str_replace('\\', '_', get_class($this->owner)), '\\'));
	}
}
