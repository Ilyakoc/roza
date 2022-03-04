<?php
namespace common\ext\nestedset\components\models;

use common\components\helpers\HArray as A;

class NestedSetModel extends \common\components\base\ActiveRecord
{
	/**
	 * (non-PHPdoc)
	 * @see \CModel::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [
			'nestedSetBehavior'=>'\common\ext\nestedset\behaviors\NestedSetBehavior',
			'sortBehavior'=>'\common\ext\sort\behaviors\SortBehavior',
			'publishedBehavior'=>'\common\ext\active\behaviors\PublishedBehavior',
		]);
	}
}