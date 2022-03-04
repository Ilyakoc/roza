<?php
/**
 * Настройки элемента области применения. 
 *
 */
use common\components\helpers\HArray as A;

class RangeofItemSettings extends \common\components\base\FormModel
{
	/**
	 * @var string идентификатор элемента.
	 */
	public $id=1;
	
	/**
	 * @var string символьный код области применения. 
	 */ 
	public $code='';
	
	/**
	 * @var boolean отображать на сайте
	 */
	public $active=false;
	
	/**
	 * @var string заголовок. 
	 */ 
	public $title='';
	
	/**
	 * @var string изображение. 
	 */ 
	public $image=null;
	
	/**
	 * @var string ссылка на страницу. 
	 */ 
	public $url='';	

	/**
	 * @var boolean для совместимости со старым виджетом 
	 * редактора admin.widget.EditWidget.TinyMCE
	 */
	public $isNewRecord=false;
	
	/**
	 * (non-PHPdoc)
	 * @see \CFormModel::__construct()
	 */
	public function __construct($scenario='')
	{
		parent::__construct($scenario);
		
		$this->id=uniqid('id');
	}
	
	/**
	 * Для совместимости со старым виджетом 
	 * редактора admin.widget.EditWidget.TinyMCE
	 */
	public function tableName()
	{
		return 'rangeof_items_settings';
	}
		
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
			'imageBehavior'=>[
    			'class'=>'\common\ext\file\behaviors\FileBehavior',
    			'attribute'=>'image',
				'attributeId'=>'id',
    			'attributeLabel'=>'Изображение',
    			'imageMode'=>true,
				'forcyGetFile'=>true
    		],	
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
			['id,active,title,url,code', 'safe']
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
			'active'=>'Отображать на сайте',	
			'code'=>'Символьный код',	
			'title'=>'Заголовок',	
			'url'=>'Ссылка'	
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \settings\components\base\SettingsModel::save()
	 */
	public function save()
	{
		$this->imageBehavior->beforeSave();
		$this->imageBehavior->afterSave();
		return true;
	}
}