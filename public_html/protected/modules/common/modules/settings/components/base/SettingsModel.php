<?php
/**
 * Базовый класс для моделей настроек.
 * 
 */
namespace settings\components\base;

use common\components\helpers\HArray as A;
use settings\components\helpers\HSettings;

abstract class SettingsModel extends \common\components\base\FormModel
{
	/**
	 * @var integer pseudo id.
	 */
	public $id=1;

	/**
	 * @var string идентификатор конфигурации.
	 */
	private $_configId=null;
	
	/**
	 * (non-PHPdoc)
	 * @see \common\components\base\FormModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
			'settingsBehavior'=>[
				'class'=>'\common\behaviors\SettingsBehavior',
				'category'=>$this->getSettingsCategory()
			],
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([]);
	}
	
	/**
	 * Получить имя категории настроек.
	 * @return string
	 */
	public function getSettingsCategory()
	{
		return \CHtml::modelName(get_called_class());		
	}
	
	/**
	 * Получить значение параметра конфигурации.
	 * @param string $name имя параметра.
	 * @param mixed|NULL $default значение по умолчанию.
	 * По умолчанию NULL.
	 * @return mixed
	 */
	public function getConfigParam($name, $default=null)
	{
		if(!$this->_configId) {
			if($config=HSettings::config()) {
				$className=get_called_class();
				foreach($config as $id=>$params) {
					if($className === trim(A::get($params, 'class'), '\\')) {
						$this->_configId=$id;
					}
				}
			}			
		}
		
		if($this->_configId) {
			return HSettings::param($this->_configId, $name, $default);
		}
		
		return $default;
	}
	
	/**
	 * Сохранение
	 */
	public function save()
	{
		parent::save();
		
		$this->saveSettings();
	
		return true;
	}
}