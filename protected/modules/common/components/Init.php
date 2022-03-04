<?php
/**
 * Обязательный компонент инициализации модуля.
 * 
 * Подключение в файле конфигурации:
 * 'preload'=>[
 * 	'kontur_common_init'
 * ],
 * 'components'=>[
 * 	'kontur_common_init'=>['class'=>'\common\components\Init']
 * ]
 */
namespace common\components;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HEvent;
use common\components\helpers\HFile;

class Init extends \CComponent
{
	protected $assetsBaseUrl=null;

	public function getAssetsBaseUrl()
	{
		if($this->assetsBaseUrl === null) {
			$this->assetsBaseUrl=Y::publish(\Yii::app()->getModule('common')->getBasePath() . Y::DS . 'assets');
		}
		return $this->assetsBaseUrl;
	}

	public function init()
	{
        $this->registerEvents();
	}

    
    public function registerEvents($modules=false, $modulePath=false)
    {
        if($modules === false) {
            $modules=\Yii::app()->getModules();
        }
        if($modulePath === false) {
            $modulePath=\Yii::app()->modulePath;
        }
        
        if(!empty($modules)) {
            foreach($modules as $name=>$config) {
                if(!is_array($config)) $name=$config;
                // загрузка дополнительных событий
                $eventsFile=HFile::path([$modulePath, $name,  'config', 'events.php']);
                if(is_file($eventsFile)) {
                    HEvent::registerByConfig(HFile::includeFile($eventsFile, []));
                }
                
                $submodules=A::get($config, 'modules', []);
                $configFile=HFile::path([$modulePath, $name,  'config', 'main.php']);
                if(is_file($configFile)) {
                    $submodules=A::m($submodules, A::get(HFile::includeFile($configFile, []), 'modules', []));
                }
                
                if(!empty($submodules)) {
                    $this->registerEvents($submodules, HFile::path([$modulePath, $name, 'modules']));
                }
            }
        }
    }
} 
