<?php
/**
 * События
 */
namespace common\components;

class Event
{
    use \common\traits\Singleton;
    
    private $events=[];
    
    public function attachEventHandler($name, $handler, $index=false)
    {
        if(!isset($this->events[$name])) {
            $this->events[$name]=new \CList;
        }
        
        if($index === false) {
            $this->events[$name]->add($handler);
        }
        else {
            $this->events[$name]->insertAt($index, $handler);
        }
    }
    
    public function raiseEvent($name, \CEvent $event)
    {
        if(isset($this->events[$name])) {
            foreach($this->events[$name] as $handler) {
                call_user_func($handler, $event);
            }
        }
    }
}
