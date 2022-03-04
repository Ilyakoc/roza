<?php
/**
 * Currency helper
 */
namespace common\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class HCurrency
{
	const EUR='EUR';
	
	/**
	 * @var string ссылка на сервис ежедневной информации
	 * @link https://www.cbr.ru/scripts/Root.asp?PrtId=DWS 
	 */
	public static $cbrDailyInfoUrl='http://www.cbr.ru/dailyinfowebserv/dailyinfo.asmx?wsdl';
	
	/**
	 * Псевдоним для HCurrency::cbrGetCursCurrent()
	 * @param string $code код валюты (напр. USD, EUR)
	 * @param boolean $float получить результат как float. По умолчанию (FALSE) - строка.
	 */
	public static function get($code, $float=false)
	{
		return self::cbrGetCursCurrent($code, $float);
	}
	
	/**
	 * Получить объект SoapClient
	 * @return \SoapClient
	 */
	public static function cbrGetSoapClient()
	{
		return new \SoapClient(self::$cbrDailyInfoUrl);
	}
	
	/**
	 * Получить курс валюты.
	 * @param string $code код валюты (напр. USD, EUR)
	 * Список кодов: http://www.cbr.ru/scripts/XML_valFull.asp
	 * @param string|NULL $date дата курса в формате YYYY-MM-DD. По умолчанию (NULL) - текущая.
	 * @param boolean $float получить результат как float. По умолчанию (FALSE) - строка.
	 * @param boolean $cache использовать кэширование. По умолчанию (TRUE) - использовать.
	 * @return string|float|NULL
	 */
	public static function cbrGetCurs($code, $date=null, $float=false, $cache=true)
	{
		$rate=null;
		
		$cacheId='cbrrate_'.$code.($float?'_f':'');
		if(!YII_DEBUG && $cache && ($data=Y::cache()->get($cacheId))) {
			if(A::get($data, 'date') == date('Y-m-d')) { 
				return A::get($data, 'rate');
			}
		}
		
		$soap=self::cbrGetSoapClient();
		$response=$soap->GetCursOnDateXML(['On_date'=>$date?:date('Y-m-d')]);
		$xml=$response->GetCursOnDateXMLResult->any;

		$xmldoc=new \DOMDocument();
        if($xmldoc->loadXML($xml)) {
            $xpath=new \DOMXPath($xmldoc);
            $nodes=$xpath->evaluate('/ValuteData/ValuteCursOnDate[VchCode="'.$code.'"]/Vcurs');
            if($node=$nodes->item(0)) {
            	$rate=$float ? (float)str_replace(',', '.', $node->nodeValue) : number_format($node->nodeValue, 2, ',', ' ');
				Y::cache()->set($cacheId, ['date'=>date('Y-m-d'), 'rate'=>$rate]);
            }
		}
		
		return $rate;
	}
	
	/**
	 * Получить курс валюты на текущий день.
	 * @param string $code код валюты (напр. USD, EUR)
	 * @param boolean $float получить результат как float. По умолчанию (FALSE) - строка.
	 * @param boolean $cache использовать кэширование. По умолчанию (TRUE) - использовать.
	 * @return string|float|NULL
	 */
	public static function cbrGetCursCurrent($code, $float=false, $cache=true)
	{
		return self::cbrGetCurs($code, null, $float, $cache);
	}	
}
