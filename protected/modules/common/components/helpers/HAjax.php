<?php
/**
 * Ajax helper
 */
namespace common\components\helpers;

class HAjax
{
	/**
	 * Start ajax session
	 * @return HAjaxResponce  
	 */
	public static function start()
	{ 
		return new HAjaxResponse();
	}
	
	/**
	 * Завершить приложение.
	 * @param boolean $success результат. По умолчанию TRUE.
	 * @param array $data массив данных. По умолчанию пустой массив.
	 * @param array $errors ошибки. По умолчанию пустой массив.
	 */
	public static function end($success=true, $data=[], $errors=[])
	{
		echo \CJSON::encode([
			'success'=>(bool)$success,
			'data'=>$data,
			'errors'=>$errors,
			'hasErrors'=>!empty($errors)
		]);
		
		\Yii::app()->end();
	}
}

/**
 * Ajax response class
 *
 */
class HAjaxResponse {
	/**
	 * @var boolean статус. Default is FALSE.
	 */
	public $success = false;
	
	/**
	 * @var mixed данные. Default is NULL.
	 */
	public $data = null;
	
	/**
	 * @var boolean были ли ошибки? Default is FALSE.
	 */
	public $hasErrors = false;
	
	/**
	 * 
	 * @var array массив ошибок array(code=>message). Default is array().
	 */
	public $errors = array();
	
	/**
	 * Add error
	 * @param string $message error message.
	 * @param number $code error code.
	 */
	public function addError($message, $code=0)
	{
		$this->hasErrors = true;
		
		if($code) 
			$this->errors[$code] = $message;
		else 
			$this->errors[] = $message;
	}
	
	public function addErrors($errors, $replace=false)
	{
		if(!empty($errors)) {
			$this->hasErrors = true;
			if(is_array($errors)) {
				if($replace) 
					$this->errors=$errors;
				else 
					$this->errors+=$errors;
			}
			else {
				if($replace) 
					$this->errors=[$errors];
				else
					$this->errors[]=$errors;
			}
		}
	}
	
	/**
	 * End ajax session
	 */
	public function end($returnOutput=false)
	{
		$data = array(
			'success'=>$this->success,
			'data'=>$this->data,
			'hasErrors'=>$this->hasErrors,
			'errors'=>$this->errors
		);
		
		if($returnOutput) return $data;
		
		echo \CJSON::encode($data);
		
		\Yii::app()->end();
	}
}
/**
 * Ajax response class
 * Устаревший класс с опечаткой в имени класса.
 * Для поддержки старых версий.
 */
class HAjaxResponce extends HAjaxResponse 
{	
}