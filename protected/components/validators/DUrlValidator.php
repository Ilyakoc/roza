<?php
/**
 * Валидатор ЧПУ для DishCMS
 */
class DUrlValidator extends \CValidator
{
	/**
	 * @var string паттерн проверки ЧПУ.
	 */
	public $pattern='/^[a-z\-\d]*$/u';
	
	/**
	 * @var string паттерн проверки ЧПУ для \CValidator::clientValidateAttribute().
	 */
	public $clientPattern='/^[a-z\-\d]*$/';
	
	/**
	 * @var boolean регистро-(не)зависимость. По умолчанию FALSE (регистро-независимый).
	 */
	public $caseSensitive=false;
	
	/**
	 * @var string текст сообщения об ошибке
	 */
	public $message='{attribute} может содержать только латинские символы, цифры и символ "-"';
	
	/**
	 * (non-PHPdoc)
	 * @see CValidator::validateAttribute()
	 */
	protected function validateAttribute($object, $attribute)
	{
		if(!preg_match($this->pattern.($this->caseSensitive?'':'i'), $object->$attribute)) {
			$this->addError($object, $attribute, $this->message);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CValidator::clientValidateAttribute()
	 */
	public function clientValidateAttribute($object, $attribute)
	{
		$message=str_replace('{attribute}', $object->getAttributeLabel($attribute), $this->message);
		return 'if(!value.match('.$this->clientPattern.($this->caseSensitive?'':'i').')) { messages.push('.CJSON::encode($message).'); }';
	}
}