Инструкция по установке и использованию модуля
----------------------------------------------------------------------------
Содержание:
I. УСТАНОВКА
II. ИСПОЛЬЗОВАНИЕ

----------------------------------------------------------------------------
I. УСТАНОВКА
----------------------------------------------------------------------------

!ВАЖНО! Модель должна быть или необходимо унаследовать от класса \common\components\base\ActiveRecord

1) Скопируйте файлы из папки /install в корень сайта.

2) Подключите и настройте модуль в /protected/config/defaults.php
(* проверить!) ВАЖНО! Должен подключатся после модуля "settings".
'modules'=>[
    ...
    'crud'=>[
        'class'=>'application.modules.crud.CrudModule',
        'config'=>[
            '<id конфигурации>'=>'<путь к файлу конфигурации>',
            '<путь к файлу общей конфигурации>'
        ]
    ],
    ...
],

Пример:
'crud'=>[
    'class'=>'application.modules.crud.CrudModule',
    'config'=>[
        'example'=>'application.modules.crud.config.crud.example',
        'application.modules.crud.config.crud.main'
    ]
],

В DishCMS>=2.4 настройки конфигурации CRUD вынесены в отдельный файл /protected/config/crud.php

----------------------------------------------------------------------------
II. ИСПОЛЬЗОВАНИЕ
----------------------------------------------------------------------------
Пример полной конфигурации с пояснениями в файле protected\modules\common\modules\crud\config\crud\example.php
Файлы конфигурации по соглашению размещаются в папке config/crud

Примеры модели со связью в модуле slider (extend\modules\slider)
Дополнительные примеры:
модуль Сопутствующие товары: protected\modules\ecommerce\modules\concurrentGoods
модуль Банки: protected\modules\extend\modules\banks
модуль Страницы: protected\modules\extend\modules\pages

Получение пункта меню HCrud::getMenuItems(Y::controller(), 'myid', 'crud/index', true)

Реализации публичной части на данный момент нет, поэтому все примеры можно найти только в примерах выше.