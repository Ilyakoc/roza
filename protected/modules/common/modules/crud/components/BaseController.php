<?php
/**
 * Основной класс для контроллеров модуля администрирования модуля
 *
 */
namespace crud\components;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

abstract class BaseController extends \Controller
{
	/**
	 * @var string путь к шаблонам контроллера.
	 */
	public $viewPathPrefix='crud.views.';
	
	/**
	 * (non-PHPdoc)
	 * @see \CController::behaviors()
	 */
	public function behaviors()
	{
		return A::m(parent::behaviors(), [			
		]);
	} 
	
	/**
	 * (non-PHPDoc)
	 * @see \CController::__construct()
	 */
	public function __construct($id, $module=null)
	{
		Y::module('common.crud');
		
		parent::__construct($id, $module);
	}
}