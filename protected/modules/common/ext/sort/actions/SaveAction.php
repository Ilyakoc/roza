<?php
namespace common\ext\sort\actions;

use common\components\helpers\HYii as Y;
use common\components\helpers\HRequest;
use common\components\helpers\HAjax;
use common\ext\sort\models\SortCategory; 
use common\ext\sort\models\SortData;

class SaveAction extends \CAction
{
	/**
	 * @var array список разрешенных категорий. 
	 */
	public $categories=[];
	
	/**
	 * (non-PHPdoc)
	 * @see \CAction::run()
	 */
	public function run()
	{
		$categoryName=Y::request()->getParam('category');
		$categoryKey=Y::request()->getParam('key') ?: null;
		
		if(!$categoryName || !in_array($categoryName, $this->categories)) {
			HRequest::e400();
		}

		$category=SortCategory::model()->category($categoryName, $categoryKey)->find();
		if(!$category) {
			$category=new SortCategory;
			$category->name=$categoryName;
			$category->key=$categoryKey ?: null;
			$category->save();
		}
		
		SortData::model()->saveData(
            $category->id, 
            Y::request()->getParam('data'),
            Y::request()->getParam('level', 0)
        );
		
		if(HRequest::isAjaxRequest()) {
			HAjax::end();
		}
	}
}
