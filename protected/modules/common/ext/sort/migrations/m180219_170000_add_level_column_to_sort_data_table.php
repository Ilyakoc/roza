<?php

class m180219_170000_add_level_column_to_sort_data_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('sort_data', 'level', 'integer');
	}

	public function down()
	{
		echo "m180219_170000_add_level_column_to_sort_data_table does not support migration down.\n";
		// $this->dropTable('sort_data');
	}
}
