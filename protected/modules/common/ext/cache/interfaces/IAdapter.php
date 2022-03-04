<?php
namespace common\ext\cache\interfaces;

interface IAdapter 
{
	/**
	 * Начало кэширования фрагмента кода отображения модели.
	 * @param \CModel $model объект модели.
	 * @param string|NULL $attribute название атрибута, который будет 
	 * использован в качестве id модели. Может быть передано NULL, что означает, 
	 * что фрагментирование по id не требуется. По умолчанию "id".
	 * @param string $hash дополнительная хэш-строка для генерации идентификатора.
 	 * @param array[\CCacheDependency|NULL] $dependency объект зависимости, который будет использван.
	 */
	public function beginCacheModel($model, $attribute='id', $hash='', $dependency=null);
	
	/**
	 * Окончание кэширования фрагмента кода отображения модели.
	 */
	public function endCacheModel();
	
	/**
	 * Обновить запись кэша элемента.
	 * @param array $options параметры для обновления
	 */
	public function update($options=[]);
	
	/**
	 * Удалить запись кэша элемента.
	 * @param array $options параметры для обновления
	 */
	public function delete($options=[]);
}