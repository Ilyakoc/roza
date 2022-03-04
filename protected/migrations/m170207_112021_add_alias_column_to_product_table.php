<?php

class m170207_112021_add_alias_column_to_product_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('product', 'alias', 'string');
	}

	public function down()
	{
		echo "m170207_112021_add_alias_column_to_product_table does not support migration down.\n";
//		return false;
	}
}