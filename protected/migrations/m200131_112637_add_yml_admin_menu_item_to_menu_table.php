<?php

class m200131_112637_add_yml_admin_menu_item_to_menu_table extends CDbMigration
{
	public function up()
	{
		$this->insert('menu', [
			'title'=>'Выгрузка в YML',
			'type'=>'model',
			'options'=>'{"model":"yml"}',
			'ordering'=>-1,
			'default'=>0,
			'hidden'=>1
		]);
	}

	public function down()
	{
		echo "m200131_112637_add_yml_admin_menu_item_to_menu_table does not support migration down.\n";
//		return false;
	}
}
