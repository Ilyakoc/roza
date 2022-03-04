<?php

class m150608_142036_create_table_related_category extends CDbMigration
{
	public function safeUp()
	{
		$this->createTable('related_category', array(
			'id'=>'pk',
			'category_id'=>'integer',
			'product_id'=>'integer'
		));
		$this->createIndex('idx_cp', 'related_category', 'category_id, product_id', true);
	}

	public function safeDown()
	{
		$this->dropTable('related_category');
	}
}