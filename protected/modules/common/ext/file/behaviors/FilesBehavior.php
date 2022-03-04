<?php
/**
 * Поведение связи файлов.
 * @todo в разработке
 */
namespace common\ext\file\behaviors;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;

class FilesBehavior extends \CBehavior
{
	/**
	 * @var string имя связи. 
	 * По умолчанию "files".
	 */
	public $rel='files';
	
	/**
	 * @var boolean может содержать только один файл.
	 * По умолчанию (FALSE) может содержать несколько файлов.
	 */
	public $one=false;
	
	/**
	 * (non-PHPdoc)
	 * @see \CBehavior::attach()
	 */
	public function attach($owner)
	{
		parent::attach($owner);
		
		HDb::migrate('common.ext.file.migrations');
	}
	
	/**
	 * Дополнительные связи для модели. 
	 * @return array
	 */
	public function relations()
	{
		return [
			$this->rel=>[
				$this->one ? \CActiveRecord::HAS_ONE : \CActiveRecord::HAS_MANY, 
				'\common\ext\file\models\File', 
				'model_id', 
				'condition'=>'model_class=:modelClass AND rel=:rel',
				'params'=>[':modelClass'=>get_class($this->owner), ':rel'=>$this->rel]					
			]
		];
	}
}
