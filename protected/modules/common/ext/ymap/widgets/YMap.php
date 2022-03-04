<?php
/**
 * Виджет карты
 */
namespace common\ext\ymap\widgets;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;
use common\components\helpers\HHash;
use common\ext\ymap\components\helpers\HYMap;

class YMap extends \common\components\base\Widget
{
	/**
	 * @var array массив параметров инициализации виджета
 	 * 	x - координата X центра (обязательный).
 	 * 	y - координата Y центра (обязательный).
 	 *  geocode - адрес или список адресов. Если передан, координаты "x" и "y" можно не передавать.
 	 *  points - список точек координат на карте.
	 *  id - идентификатор контейнера карты.
	 * 	zoom - значение параметра увеличения. По умолчанию 17.
	 * 	hint - подсказка балуна. По умчоланию не задана.
	 * 	content - содержимое балуна. По умолчанию не задано. 
	 * 	iconImageHref - ссылка на картинку балуна.
	 *  iconImageSize - размеры картинки балуна. 
	 */
	public $options=[];
	
	/**
	 * @var string подпись
	 */	
	public $label = false;
	
	/**
	 * @var array массив атрибутов для контейнера подписи.
	 */
	public $labelOptions = [];
	
	/**
	 * @var string имя шаблона отображения карты.
	 */
	public $view = 'ymap_default';
	
	/**
	 * {@inheritDoc}
	 * @see CWidget::init()
	 */
	public function init()
	{
		$this->htmlOptions['id']=$this->getMapId();
		
		HYMap::registerYMap();
		
		Y::publish([
			'path'=>HFile::path([dirname(__FILE__), 'assets', 'ymap']),
			'js'=>'scripts.js'
		]);
		
		$x=A::get($this->options, 'x', false);
		$y=A::get($this->options, 'y', false);
		$geocode=A::get($this->options, 'geocode', false);
		
		if(!($geocode || ((float)$x && (float)$y))) {
			return false;
		}
		
		if($geocode) {
			if(is_array($geocode)) {
				$this->options['points']=[];
				foreach($geocode as $address) {
					$this->options['points'][]=json_encode($this->getGetCoordsByGeoCode($address));
				}
				$geocode=false;
			}
			elseif($coords=$this->getGetCoordsByGeoCode($geocode)) {
				$geocode=false;
				$x=$coords[1];
				$y=$coords[0];
			}
		}
		
		$options=[
			'id'=>$this->getMapId(),
			'x'=>$x,
			'y'=>$y,
			'geocode'=>$geocode,
			'zoom'=>A::get($this->options, 'zoom', 17),
			'hint'=>A::get($this->options, 'hint', false),
			'content'=>A::get($this->options, 'content', false),
			'iconImageHref'=>A::get($this->options, 'iconImageHref', false),
			'iconImageSize'=>A::get($this->options, 'iconImageSize', [40,40]),
			'points'=>A::get($this->options, 'points', false),
		];
		//Y::js(false, 'Сommon_Ext_YMap_Widgets_YMap('.\CJavaScript::encode($options).')');
		$_opts=[];
		foreach($options as $k=>$v) $_opts[]=$k.':'.(is_numeric($v)?$v:(is_array($v)?('['.implode(',',$v).']'):"\"{$v}\""));
		Y::js(false, ';new Сommon_Ext_YMap_Widgets_YMap({'.implode(',',$_opts).'});');
	}
	
	/**
	 * {@inheritDoc}
	 * @see \common\components\base\Widget::run()
	 */
	public function run()
	{
		if(!A::get($this->options, 'geocode', false) 
			&& (!(float)A::get($this->options, 'x', false)
				|| !(float)A::get($this->options, 'y', false)))
		{
			return false;
		}
		
		if(!isset($this->htmlOptions['id'])) {
			$this->htmlOptions['id']=$this->getMapId();
		}
		
		$this->render($this->view, $this->params);
	}
	
	/**
	 * Получить идентификатор контейнера карты
	 * @return string|false
	 */
	public function getMapId()
	{
		if(!A::get($this->options, 'id')) {
			$this->options['id']=HHash::ujs();
		}
		
		return $this->options['id'];
	}
	
	public function getGetCoordsByGeoCode($geocode)
	{
		$coords=false;
		
		if($coords=Y::cache()->get("ymap_geocode_".md5($geocode))) {
			return $coords;
		}
		
		$ch=curl_init('https://geocode-maps.yandex.ru/1.x/?format=json&geocode='.$geocode);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		if($data=curl_exec($ch)) {
			$result=json_decode($data);
			try {
				if(count($result->response->GeoObjectCollection->featureMember) > 0) {
					$coords=explode(" ", $result->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);
					Y::cache()->set("ymap_geocode_".md5($geocode), $coords);
				}
			}
			catch(\Exception $e) {
			}
		}
		
		curl_close($ch);
			
		return $coords;
	}
}