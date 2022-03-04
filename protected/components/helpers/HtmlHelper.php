<?php
/**
 * HTML helper
 * 
 * @version 1.04
 * 
 * @history
 * 1.01 
 *  - add priceFormat() method.
 *  - add getIntro() method.
 *  - modify linkBack() method, add second parameter $defaultBackUrl. 
 * 1.02
 *  - add parameter $detailLink to getIntro() method.
 * 1.03
 *  - add parameter $prefixSpace to AttributesToString() method.
 * 1.04
 *	- add method numberFormat().
 */
class HtmlHelper extends CComponent
{
	/**
	 * Convert array of html tag attributes to string
	 * @param array $attributes html tag attributes.
	 * @param bool $forcibly include or not attributes with empty values into result string. 
	 * Default (false) not including. 
	 * @param bool $prefixSpace insert or not, space to begin output string. default TRUE.
	 * @return string html tag attributes.
	 */
	public static function AttributesToString($attributes, $forcibly=false, $prefixSpace=true)
	{
		$_attributes = array();
		foreach($attributes as $attribute=>$value) {
			if($value || $forcibly) 
				$_attributes[] = $attribute . '="' . preg_replace('/\\\\*?"/', '\"', $value) . '"';
		}
		
		return empty($_attributes) ? '' : ($prefixSpace ? ' ' : '') . implode(' ', $_attributes);
	}
	
	/**
	 * Ссылка на предыдущую страницу
	 * @param string $text текст ссылки
	 * @param string $defaultBackUrl ссылка возврата по умолчанию, 
	 * если переход был по прямой ссылке.
	 * @param string $path путь. возврат идет на урл, если referrer, содержит этот путь.
	 * @param array $htmlOptions link html options. Default empty array.
	 * @return string
	 */
	public static function linkBack($text='Go to back', $defaultBackUrl='/', $path='', $htmlOptions=[]) 
	{ 
		$link = \Yii::app()->request->urlReferrer;

		if(!preg_match('/^[^\/]+:\/\/'.\Yii::app()->request->serverName.($path?str_replace('/', '\/',$path):'').'(.*)$/i', $link)) {
			$link = $defaultBackUrl;
		} 
		
		return CHtml::link($text, $link, $htmlOptions);
	}
	
	/**
	 * Print yii application user flash.
	 * @see \CWebUser::getFlash()
	 * @param string $key 
	 * @param string $defaultValue
	 * @param boolean $delete
	 * 
	 * @param string $return Возвращать или нет HTML-код сообщения. 
	 * @return string|void
	 */
	public static function flash($key, $defaultValue=NULL, $delete=true, $return=false)
	{
		$html = '';
		
		if(\Yii::app()->user->hasFlash($key)) {
			$html = "<div class=\"flashMessage {$key}\">";
			$html .= \Yii::app()->user->getFlash($key);
			$html .= '</div>';
		}
		
		if($return) return $html;
		
		echo $html;
	}
	
	/**
	 * Вывод отформатированной цены
	 * @param sting $price цена.
	 * @return string
	 */
	public static function priceFormat($price)
	{
		return self::numberFormat($price);
	}
	
	/**
	 * Вывод отформатированного числа
	 * @param sting $number число.
	 * @return string
	 */
	public static function numberFormat($number)
	{
		$decimal = (int)$number < (float)$number ? 2 : 0;
		return number_format($number, $decimal, '.', ' ');
	}
	
	public static function getActiveClass() 
	{
		$controllers = func_get_args();
		$current = Yii::app()->controller->getId();
		foreach($controllers as $controller){
			if($controller == $current){
				return 'active';
			}
		}
	}
	
	/**
	 * Получить анонс текста
	 * @param string $text основной текст.
	 * @param int $length длина анонса.
	 * @param string $detailLink ссылка подробнее
	 * @return string
	 */
	public static function getIntro($text, $length=300, $detailLink='...')
	{
		if(!is_numeric($length)) $length = 300;
		
		$subLimit = floor($length / 10);
		$text = strip_tags($text);
		if(mb_strlen($text) > $length) {
			$text = preg_replace('/ +/', ' ', mb_substr($text, 0, $length));
			$chunks = explode(' ', $text);
			$count  = count($chunks);
			$chunks = array_slice($chunks, 0, ($count <= $subLimit) ? ($count - 1) : $subLimit);
			$text = implode(' ', $chunks) . $detailLink;
		}
		
		return $text;
	}

	public static function getMarkCssClass($model, $attributes) 
	{ 
		return ($attribute=array_shift($attributes)) ? ($model->$attribute ? " {$attribute}" : self::getMarkCssClass($model, $attributes)) : ''; 
	}
}