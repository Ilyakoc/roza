<?php

class m170207_125953_drop_column_meta_desc_from_category_table extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('category', 'meta_desc');
	}

	public function down()
	{
		echo "m170207_125953_drop_column_meta_desc_from_category_table does not support migration down.\n";
//		return false;
	}
}