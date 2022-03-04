<?php

class m170130_042939_create_seo_table extends CDbMigration
{
	public $table='seo_seo';
	
	/**
	 * (non-PHPdoc)
	 * @see CDbMigration::safeUp()
	 */
	public function safeUp()
	{
		$this->createTable('seo_seo', [
			'id'=>'pk',
			'hash'=>'BIGINT',
			'model_name'=>'string NOT NULL DEFAULT \'\'',
			'model_id'=>'integer NOT NULL DEFAULT 0',
			'h1'=>'string',
			'meta_title'=>'string',
			'meta_keywords'=>'string',
			'meta_description'=>'TEXT',
			'link_title'=>'string',
			'update_time'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
		]);
		
		$this->createIndex('hash', $this->table, 'hash');
	}

	/**
	 * (non-PHPdoc)
	 * @see CDbMigration::safeDown()
	 */
	public function safeDown()
	{
		// $this->dropTable('seo');
		echo "m170130_042939_create_seo_table does not support migration down.\n";
		// return false;
	}
}