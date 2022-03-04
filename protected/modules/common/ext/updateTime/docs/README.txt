Подключение
-----------
1) Добавить поведение в модель (модель должны наследоваться от \common\components\base\ActiveRecord)
use common\components\helpers\HArray as A;

public function behaviors()
{
    return A::m(parent::behaviors(), [
    	'udpateTimeBehavior'=>array('class'=>'\common\ext\updateTime\behaviors\UpdateTimeBehavior')
    ]);
}

Использование
-------------
1) Послать заголовок последней модификации страницы Last-Modified
$model->udpateTimeBehavior->sendLastModified();

2) Отложенная отравка заголовка последней модификации страницы Last-Modified
Необходима для актуальной информации времени последней модификации страницы, если на странице отображается информация
с использованием нескольких моделей данных.

При использовании отложенной отправки заголовка модификации страницы Last-Modified необходимо
2.1) В файле webroot/index.php добавить дополнительный перехват буфера вывода
<?php
ob_start();
...
Yii::createWebApplication($config)->run();
ob_end_flush();

2.2) В шаблонах отображения добавить для соответсвующих моделей код 
$model->udpateTimeBehavior->sendLastModified(true);

3) Отображение последней даты модификации модели.
<? $this->widget('\common\ext\updateTime\widgets\UpdatedInfo', ['datetime'=>$model->update_time]); ?>