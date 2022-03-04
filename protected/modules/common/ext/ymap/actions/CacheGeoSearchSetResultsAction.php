<?php
/**
 * Действие кэширования результатов геопоиска по организациям для Яндекс.Карты.
 * 
 */
namespace common\ext\ymap\actions;

use common\components\helpers\HRequest as R;
use common\ext\ymap\components\helpers\HYMap;
use common\components\helpers\HAjax;

class CacheGeoSearchSetResultsAction extends \CAction
{
	/**
	 * {@inheritDoc}
	 * @see CAction::run()
	 */
	public function run()
	{
		$hash=R::rget('hash', false, true);
		$data=R::rget('data', false, true);
		
		if(($hash !== false) && ($data !== false)) {
			HYMap::setGeoSearchData($hash, $data);
		}
		
		HAjax::end();
	}
}