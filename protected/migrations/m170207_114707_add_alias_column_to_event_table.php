<?php

class m170207_114707_add_alias_column_to_event_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('event', 'alias', 'string');
	}

	public function down()
	{
		echo "m170207_114707_add_alias_column_to_event_table does not support migration down.\n";
//		return false;
	}
}