<?php
/**
 * Информация о дате обновления.
 */
namespace common\ext\updateTime\widgets;

use common\components\helpers\HYii as Y;

class UpdatedInfo extends \CWidget
{
	/**
	 * @var string дата в формате TIMESTAMP.
	 */
	public $datetime;
	
	/**
	 * @var string вторая дата в формате TIMESTAMP. С этой датой будет
	 * сравниваться основная и если основная окажется более ранней, то
	 * отображена будет эта.
	 * Полезно, если вторая дата получена из зависимости, например 
	 * для Категории отображать также дату обновления товара в Категории.  
	 */
	public $datetime2=null;
	
	/**
	 * @var string имя тэга обертки.
	 */
	public $tag='div';
	
	/**
	 * @var array HTML опции для тэга.
	 */
	public $htmlOptions=['class'=>'update_time'];
	
	/**
	 * @var string имя шаблона отображения.
	 */
	public $view='update_info';
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$this->render($this->view);		
	}
	
	/**
	 * Получить актуальное значение даты обновления. 
	 */
	public function getDateTime()
	{
		if(!$this->datetime2 || (Y::dateTimeDiff($this->datetime, $this->datetime2) > 0)) {
			return $this->datetime;
		}
		else {
			return $this->datetime2;
		}
	}
}