<?php

class m170310_045028_create_file_files_table extends CDbMigration
{
	public function safeUp()
	{
		$sql='CREATE TABLE `file_files` (
			`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`hash` BIGINT, 
			`filehash` BIGINT,
			`model_class` VARCHAR(255),
			`model_id` INT(11),
			`rel` VARCHAR(255),
			`file` VARCHAR(255),
			`title` VARCHAR(255),
			`description` TEXT,
			`sef` VARCHAR(255),
			`html_title` VARCHAR(255),
			`published` TINYINT(1) NOT NULL DEFAULT 0,
			`update_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			UNIQUE (`hash`),			
			UNIQUE (`filehash`)		
		)';
		$this->execute($sql);		
	}

	public function down()
	{
		echo "m170310_045028_create_common_ext_file_files_table does not support migration down.\n";
		// $this->dropTable('common_ext_file_files');
	}
}