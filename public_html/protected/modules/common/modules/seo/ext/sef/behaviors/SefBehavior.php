<?php
/**
 * Поведение ЧПУ 
 *
 */
namespace seo\ext\sef\behaviors;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;

class SefBehavior extends \CBehavior
{
	/**
	 * @var boolean добавить автоматически в таблицу базы данных поле для хранения данных
	 * данного атрибута. По умолчанию (TRUE) добавить.
	 */
	use \common\traits\models\AddColumn;
	
	/**
	 * @var string имя атрибута ЧПУ внешней модели
	 */
	public $attribute='sef';
	
	/**
	 * @var string название атрибута.
	 * По умолчанию (NULL) из заданных в данном расширении.
	 */
	public $attributeLabel=null;
	
	/**
	 * @var boolean уникальный или нет.
	 */
	public $unique=true;
	
	/**
	 * @var array массив моделей array(className=>attributeName) по которым также проверяется уникальность.
	 */
	public $uniqueWith=[];
	
	/**
	 * @var boolean регистро-(не)зависимость. По умолчанию FALSE (регистро-независимый).
	 */
	public $caseSensitive=false; 
	
	/**
	 * (non-PHPDoc)
	 * @see CBehavior::attach($owner)
	 */
	public function attach($owner)
	{
		parent::attach($owner);
	
		if($this->attributeLabel === null) {
			$t=Y::ct('\seo\ext\sef\Messages.common', 'extend.seo');
			$this->attributeLabel=$t('sef.label');
		}
	
		$this->addColumn($this->owner->tableName(), $this->attribute, 'string');
	}
	
	/**
	 * Scope: выборка по ЧПУ
	 * @param string $sef ЧПУ
	 * @param string $options дополнительные параметры для выборки. 
	 * "operator" (string, default:"LIKE") опрератор сравнивания (напр., "LIKE","REGEXP","=");
	 * "before" (string, default:"") дополнительная строка перед значением (напр., для "LIKE" это может быть "%"); 
	 * "after" (string, default:"") дополнительная строка после значением (напр., для "LIKE" это может быть "%");
	 * Данные параметры могут быть переданы, одним массивом, как array(operator, before, after), в этим случае
	 * если необходимо задать, например, только значение "%" для "after", необходимо передать array("LIKE", "", "%")
	 *  
	 * @return \CActiveRecord
	 */
	public function sef($sef, $options=[])
	{
		$operator=A::get($options, 'operator', A::get($options, 0, 'LIKE'));
		$before=A::get($options, 'before', A::get($options, 1, ''));
		$after=A::get($options, 'after', A::get($options, 2, ''));
		
		$this->owner->getDbCriteria()->mergeWith([
			'condition'=>HDb::qc($this->attribute) . " {$operator} :sef",
			'params'=>[':sef'=>$before.$sef.$after]
		]);
		
		return $this->owner;
	}
	
	/**
	 * Массив правил валидации
	 * @return array
	 */
	public function rules()
	{
		$rules=[
			[$this->attribute, 'length', 'max'=>255],
// 			[
// 				$this->attribute, 
// 				'\seo\ext\sef\components\validators\SefValidator', 
// 				'caseSensitive'=>$this->caseSensitive
// 			]
		];
		
		if($this->unique) {
			$rules[]=[$this->attribute, 'unique', 'caseSensitive'=>false];
		}
		
		if(!empty($this->uniqueWith)) {
			foreach($this->uniqueWith as $className=>$attributeName) 
				$rules[]=[$this->attribute, 'unique', 'caseSensitive'=>false, 'className'=>$className, 'attributeName'=>$attributeName];
		}
		
		return $rules; 
	}
	
	/**
	 * Get attribute labels.
	 */
	public function attributeLabels()
	{
		return [
			'sef'=>$this->attributeLabel,
		];
	}
}