<?php

class m180215_180000_add_column_view_template_to_category_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('category', 'view_template', 'string');
	}

	public function down()
	{
		echo "m180215_180000_add_column_view_template_to_category_table does not support migration down.\n";
//		return false;
	}
}
