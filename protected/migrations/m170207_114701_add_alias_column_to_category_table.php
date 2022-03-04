<?php

class m170207_114701_add_alias_column_to_category_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('category', 'alias', 'string');
	}

	public function down()
	{
		echo "m170207_114701_add_alias_column_to_category_table does not support migration down.\n";
//		return false;
	}
}