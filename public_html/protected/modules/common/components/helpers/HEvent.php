<?php
/**
 * Класс-помощник событий
 */
namespace common\components\helpers;

use common\components\Event;

class HEvent
{
    public static function getComponent($component=false)
    {
        if($component === false) {
            $component=Event::i();
        }
        return $component;
    }
    
    public static function registerByConfig($config, $component=false)
    {
        foreach($config as $name=>$handlers) {
            foreach($handlers as $handler) {
                static::register($name, $handler, false, $component);
            }
        }
    }
    public static function register($name, $handler, $index=false, $component=false)
    {
        static::getComponent($component)->attachEventHandler($name, $handler, $index);
    }
    
    public static function raise($name, $params=[], $component=false)
    {
        $event=new \CEvent;
        $event->params=$params;
        
        static::getComponent($component)->raiseEvent($name, $event);
        
        return $event;
    }
}
