<?php
namespace common\components\base;

use common\components\helpers\HYii as Y;

class BaseException extends \CException
{
	const E_UNKNOW=0;
	
	/**
	 * Общий вызов ошибки
	 * @param string $message
	 * @param number $code
	 * @param string $previous
	 */
	public static function e($message='', $code=0, $previous=null)
	{
		$t=Y::ct('\CommonModule.exceptions', 'common');
		if(!$message) $message=$t('error.'.$code);
	
		throw new self($message, $code, $previous);
	}
}