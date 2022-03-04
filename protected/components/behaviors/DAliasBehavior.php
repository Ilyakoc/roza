<?php
/**
 * Поведение ЧПУ для DishCMS 
 *
 */
class DAliasBehavior extends \CBehavior
{
	/**
	 * @var string имя атрибута ЧПУ внешней модели
	 */
	public $attribute='alias';
	
	/**
	 * @var boolean уникальный или нет.
	 */
	public $unique=true;
	
	/**
	 * @var array массив моделей array(className=>attributeName) по которым также проверяется уникальность.
	 */
	public $uniqueWith=array(
		'Blog'=>'alias',
		'Page'=>'alias', 
		'Category'=>'alias',
		'Product'=>'alias', 
		'Event'=>'alias'		
	);

	/**
 	 * @var array массив принадлежности моделей модулям array(className=>moduleName).
	 */ 
	public $modules=array(
		// 'Category'=>'shop',
		'Product'=>'shop'
	);
	
	/**
	 * @var boolean регистро-(не)зависимость. По умолчанию FALSE (регистро-независимый).
	 */
	public $caseSensitive=false; 
	
	/**
	 * @var string название атрибута
	 */
	public $attributeLabel='URL';	
	
	/**
	 * Массив правил валидации
	 * @return array
	 */
	public function rules()
	{
		$rules=array(
			array($this->attribute, 'length', 'max'=>255),
			array($this->attribute, 'DUrlValidator', 'caseSensitive'=>$this->caseSensitive)
		);
		
		if($this->unique) $rules[]=array($this->attribute, 'unique', 'caseSensitive'=>false);
		
		if(!empty($this->uniqueWith)) {
			foreach($this->uniqueWith as $className=>$attributeName) {
				$rules[]=array($this->attribute, 'unique', 'caseSensitive'=>false, 'className'=>$className, 'attributeName'=>$attributeName);
			}
		}
		
		return $rules; 
	}
	
	public function attributeLabels()
	{
		return array(
			$this->attribute=>$this->attributeLabel
		);
	}
}
