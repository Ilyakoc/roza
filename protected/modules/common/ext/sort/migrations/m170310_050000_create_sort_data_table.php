<?php

class m170310_050000_create_sort_data_table extends CDbMigration
{
	public function up()
	{
		$sql='CREATE TABLE IF NOT EXISTS `sort_data` (
			`id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
			`category_id` INT(11) NOT NULL,
			`model_id` INT(11) NOT NULL,
			`order_number` INT(11) NOT NULL,
			UNIQUE (`category_id`, `model_id`),
			KEY(`category_id`),
			FOREIGN KEY `category_id` (`category_id`) REFERENCES `sort_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
		)';
		
		$this->execute($sql);
	}

	public function down()
	{
		echo "m170310_050000_create_sort_data_table does not support migration down.\n";
		// $this->dropTable('sort_data');
	}
}