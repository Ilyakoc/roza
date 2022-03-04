<?php
/**
 * Реализация паттерна Singleton. 
 */
namespace common\traits;

trait Singleton 
{
	/**
	 * @var mixed статический экземпляр класса.
	 */
	public static $instance;
	
	/**
	 * Получить экземпляр класса
	 * @return mixed
	 */
	public static function getInstance()
	{
		if(!(self::$instance instanceof self)) {
			self::$instance=new static;
		}
		
		return self::$instance;
	}
	
	/**
	 * Короткий псевдоним для метода Singleton::getInstance()
	 * @return mixed
	 */
	public static function i()
	{
		return self::getInstance();
	}
}