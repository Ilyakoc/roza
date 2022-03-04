<?php
namespace common\traits\models;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;

trait AddColumn
{
	/**
	 * @var boolean добавить автоматически в таблицу базы данных поле для хранения данных
	 * данного атрибута.
	 * По умолчанию (FALSE) не добавлять.
	 */
	public $addColumn=true;
	
	/**
	 * @access protected
	 * @var array кэш запросов мета-данных таблиц.
	 */
	protected static $cacheRefreshTables=[];
	
	/**
	 * Добавление поля в таблицу базы данных.
	 * @param string $table имя таблицы
	 * @param string $attribute имя атрибута
	 * @param string $type тип
	 * @see \CDbSchema::addColumn()
	 * @return boolean
	 */
	protected function addColumn($table, $attribute, $type)
	{
		if($this->addColumn) {
			if($table=HDb::getTable($table, A::get(static::$cacheRefreshTables, $table, true))) {
				$this->addColumn=false;				
				if(!$table->getColumn($attribute)) {
					HDb::execute(HDb::schema()->addColumn($table->name, $attribute, $type));
					return true;
				}
				else {
					if($table instanceof \CMysqlTableSchema) {
						$table = $table->name;
					}
					static::$cacheRefreshTables[$table]=false;
				}
			}
		}
		
		return false;
	}
}