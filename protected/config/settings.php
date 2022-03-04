<?php
/**
 * Параметры для конфигурации модуля common\settings
 */
return [
	'shop'=>[
		'class'=>'\ShopSettings',
		'title'=>'Настройки магазина',
		'menuItemLabel'=>'Настройки',
		'breadcrumbs'=>['Каталог'=>'/cp/shop/index'],
		'viewForm'=>'admin.views.settings._shop_form'
	]
];