----------------------------------------------------
Документация для раширения \common\ext\cache
----------------------------------------------------

На данный момент работает ТОЛЬКО для моделей \CActiveRecord

------------
I. Установка 
------------

Подключить компонент в конфигурации приложения
    'kcache'=>[
		'class'=>'\common\ext\cache\components\Cache',
		'adapter'=>[
		  'class'=>'\common\ext\cache\behaviors\adapters\DbCache',
		  'duration'=>2592000
		]
	],
    
-----------------    
II. Использование
-----------------
Примеры для адаптера DbCache:

Для моделей желательно подключать поведение:
    'cacheBehavior'=>['class'=>'\common\ext\cache\behaviors\ARUpdate'],

1) Оптимизация быстродействия
-----------------------------
Для ускорения поиска закэшированных данных при выводе списка моделей можно использовать следующий код:
ВНИМАНИЕ! На данный момент, НЕЛЬЗЯ использовать вложенную оптимизацию, т.е. если кэширование идет с вложенными циклами.
перед циклом
\Yii::app()->kcache->prependCacheModel($dataProvider->getData()); <-- это для оптимизации
foreach($dataProvider->getData() as $data):
    if(\Yii::app()->kcache->beginCacheModel($data)):
    ...
    \Yii::app()->kcache->endCacheModel(); endif;
endforeach;
\Yii::app()->kcache->finalCacheModel(); <-- это завершение оптимизации, если не задать

2) Пример использования зависимостей.
-------------------------------------
Есть две таблицы 
"session" - список занятий (id, title). 
"reservation" - забронированное время (id, session_id, time)

Нужно обновлять кэш страницы бронирования занятий, если есть новые записи забронированного времени или отменена бронь.

$modelReservation=new Reservation;
$modelReservation->session_id=$modelSession->id;
if(\Yii::app()->kcache->beginCacheModel($modelReservation, 'session_id')):
    ...
\Yii::app()->kcache->endCacheModel(); endif;

3) 