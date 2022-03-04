<?php
/**
 * Базовый класс для виджетов.
 */
namespace common\components\base;

use common\components\helpers\HYii as Y;

class Widget extends \CWidget
{
	/**
	 * @var string имя шаблона представления.
	 */
	public $view;
	
	/**
	 * @var array дополнительные параметры для шаблона отображения.
	 */
	public $params=[];
	
	/**
	 * @var array дополнительные HTML-атрибуты для основного элемента.
	 */
	public $htmlOptions=[];
	
	/**
	 * @var string имя тэга обертки.
	 * Может быть передано пустое значение, тогда тэг отображаться не будет.
	 */
	public $tag='div';
	
	/**
	 * @var array дополнительные HTML-атрибуты для элемента обертки.
	 */
	public $tagOptions=['class'=>'row'];
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$this->render($this->view, $this->params);
	}
	
	/**
	 * Получить html-тэг открытия обертки.
	 * @return string
	 */
	public function openTag()
	{
		if(!empty($this->tag)) {
			return \CHtml::openTag($this->tag, $this->tagOptions);
		}
	
		return '';
	}
	
	/**
	 * Получить html-тэг закрытия обертки.
	 * @return string
	 */
	public function closeTag()
	{
		if(!empty($this->tag)) {
			return \CHtml::closeTag($this->tag);
		}
	
		return '';
	}
	
	/**
	 * Публикация ресурсов по умолчанию.
	 * @param string|array|bool $js имя файла публикуемого js-скрипта. 
	 * По умолчанию (true) будет опубликован файл "scripts.js".
	 * Если передано (false) скрипты опубликованы не будут.
	 * Может быть передан массив js-файлов.	 * 
	 * @param string|array|bool $css имя публикуемого файла css-стилей. 
	 * По умолчанию (true) будет опубликован файл "styles.css".
	 * Если передано (false) файлы стилей опубликованы не будут.
	 * Может быть передан массив файлов css-стилей.
	 * @param string|array|bool $path путь к ресурсам относительно папки assets
	 * директории класса виджета. По умолчанию (true) будет использовано
	 * имя класса без пространства имен с первой буквой в нижнем регистре.
	 * Может быть передано (false) ресурсы будут опубликованы из основной 
	 * папки "assets" директории класса виджета. Может быть передан массив,
	 * где каждый элемент, это имя папки пути. 
	 * @return string an absolute URL to the published asset 
	 */
	protected function publish($js=true, $css=true, $path=true)
	{
		$refCalledClass = new \ReflectionClass(get_called_class());
		$basePath = dirname($refCalledClass->getFileName());
		
		$config=['path'=>[$basePath, 'assets']];
		
		if($path === true) {
			$config['path'][]=lcfirst($refCalledClass->getShortName());
		}
		elseif(is_array($path)) {
			$config['path']=array_merge($config['path'], $path);
		}
		elseif($path) {
			$config['path'][]=$path;
		}
		
		if($js === true) {
			$config['js']='scripts.js';
		}
		elseif($js) {
			$config['js']=$js;
		}
		
		if($css === true) {
			$config['css']='styles.css';
		}
		elseif($css) {
			$config['css']=$css;
		}
		
		return Y::publish($config);
	}
}