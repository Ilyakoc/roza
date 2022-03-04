<?php

class m170207_120245_add_meta_h1_column_to_metadata_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('metadata', 'meta_h1', 'string');
	}

	public function down()
	{
		echo "m170207_120245_add_h1_column_to_metadata_table does not support migration down.\n";
//		return false;
	}
}