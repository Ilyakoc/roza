<?php

class m170310_045952_create_sort_category_table extends CDbMigration
{
	public function up()
	{
		$sql='CREATE TABLE IF NOT EXISTS `sort_category` (
			`id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
			`name` VARCHAR(32) NOT NULL,
			`key` INT(11),
			UNIQUE (`name`, `key`),
			INDEX (`name`))';
		
		$this->execute($sql);
	}

	public function down()
	{
		echo "m170310_045952_create_sort_category_table does not support migration down.\n";
		// $this->dropTable('sort_category');
	}
}