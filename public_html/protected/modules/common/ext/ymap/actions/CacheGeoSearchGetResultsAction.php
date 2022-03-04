<?php
/**
 * Действие получения результатов геопоиска по организациям для Яндекс.Карты из кэша.
 *
 */
namespace common\ext\ymap\actions;

use common\components\helpers\HRequest as R;
use common\ext\ymap\components\helpers\HYMap;
use common\components\helpers\HAjax;

class CacheGeoSearchGetResultsAction extends \CAction
{
	/**
	 * {@inheritDoc}
	 * @see CAction::run()
	 */
	public function run()
	{
		$ajax=HAjax::start();
		
		if($hash=R::rget('hash', false, true)) {
			$ajax->data=HYMap::getGeoSearchData($hash);
		}
		
		$ajax->success=is_array($ajax->data);
		$ajax->end();
	}
}