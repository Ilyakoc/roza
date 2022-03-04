Модуль Настроек.

--------------
1) ПОДКЛЮЧЕНИЕ
--------------
1.1) В файле \protected\config\defaults.php
    
	'modules'=>array(
		...
		'settings'=>[
			'class'=>'application.modules.settings.SettingsModule',
			'config'=>[
				'banners'=>[
					'class'=>'\BannerSettings',
					'title'=>'Настройки баннеров',
					'menuItemLabel'=>'Баннеры',
					'breadcrumbs'=>['Баннеры'=>['/cp/banners/index']],
					'viewForm'=>'admin.views.settings._banner_form'
				],
                ...
			]	
		],

 * Формат конфигурации:
 * <id настроек> => array(
 * 		'class' => (обязательный) имя класса модели настроек, 
 * 		наследуемый от \settings\components\base\SettingsModel,
 * 
 * 		'title' => заголовок настроек в разделе администрирования.
 *
 * 		'menuItemLabel' => заголовок пункта меню данных настроек 
 * 		в разделе администрирования.
 *
 * 		'breadcrumbs' => дополнительный массив для хлебных крошек 
 *		в формате array([title=>url], ...), либо array([title=>[url, param=>value]], ...)
 * 
 * 		'viewForm' => путь к шаблону формы редактирования настроек 
 * 		в разделе администрирования. Основа шаблона может быть 
 * 		взята из settings.views.default._form 
 * )

1.2) Создать модель настроек
Прототип модели находится здесь: protected\modules\settings\models\ExampleSettings.php

При использовании редактора admin.widget.EditWidget.TinyMCE, в модель необходимо 
добавить поле $isNewRecord и метод tableName()
Также он может потребоваться для некоторых поведений. Например \common\ext\dataAttribute

/**
 * @var boolean для совместимости со старым виджетом 
 * редактора admin.widget.EditWidget.TinyMCE
 */
public $isNewRecord=false;

/**
 * Для совместимости со старым виджетом 
 * редактора admin.widget.EditWidget.TinyMCE
 */
public function tableName()
{
    return 'banner_settings';
}

1.3) Скопировать и отредактировать шаблон формы
Шаблон формы находится здесь: 
protected\modules\settings\modules\admin\views\default\_form.php
Шаблон вкладок находится здесь: 
protected\modules\settings\modules\admin\views\default\_tabs.php

1.4) Добавить правила маршрутизации для раздела администрирования:
В файл protected\config\urls.php (внедрен в DishCMS >= 2.3.4.1)

'settings/admin/default/index/<id:\w+>'=>'settings/admin/default/index',
'admin/settings/<id:\w+>'=>'admin/settings/index',
'cp/settings/<id:\w+>'=>'admin/settings/index',

Ссылки на страницы администрирования, соотвественно, должны быть вида:
/admin/settings/<id настроек>

1.5) Скопировать контроллер в раздел администрирования (внедрен в DishCMS >= 2.3)
скопировать protected\modules\settings\install\protected\modules\admin\controllers\SettingsController.php
в protected\modules\admin\controllers\SettingsController.php

----------------
2) ИСПОЛЬЗОВАНИЕ
----------------

2.1) Получение списка пунктов меню для разделов администрирования 
для вставки в шаблон \settings\components\helpers\HSettings::getMenuItems($this, null, 'settings/index')
Пример (внедрен в DishCMS >= 2.3)
use common\components\helpers\HArray as A;

if(HYii::module('settings')) {
	$modulesMenu[] = ['label'=>'', 'itemOptions'=>['class'=>'divider']];
    $modulesMenu = A::m($modulesMenu, \settings\components\helpers\HSettings::getMenuItems($this, null, 'settings/index'));
}

2.2) Получение модели настроек:
$model=HSettings::getById('<id настроек>'); 

Пример:
use \settings\components\helpers\HSettings;

$banner=HSettings::getById('banners'); 