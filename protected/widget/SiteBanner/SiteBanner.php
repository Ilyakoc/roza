<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 28.08.12
 * Time: 13:25
 * To change this template use File | Settings | File Templates.
 */
class SiteBanner extends CWidget
{
	public $type = null;  // 1 - carousel, 2 - cycle (slideshow), 3 - flash

    public function run()
    {
	    if($this->type == null) {
			$firstElement = Banner::model()->find(array( 'order' => 'ordering'));
		    if($firstElement)
				$this->type = $firstElement->type;
		    else
			    return false;
		}

    	$views = array(
    		1 => 'default',
    		2 => 'slide',
		    3 => 'flash',
    	);

    	$scripts = array(
    		1 => '/js/jquery.jcarousel.min.js',
    		2 => '/js/jquery.cycle.min.js',
		    3 => '/js/jquery.flash.js',
    	);

    	if(isset($scripts[$this->type]))
        	Yii::app()->clientScript->registerScriptFile($scripts[$this->type]);
        else
			return false;

	    if($this->type !== Banner::BANNER_FLASH)
		    $banners = Banner::model()->findAll(array('order'=>'ordering', 'condition' => 'type = :type', 'params' => array(':type' => $this->type)));
	    else
		    $banners = Banner::model()->find(array( 'order' => 'ordering', 'condition' => 'type = :type', 'params' => array(':type' => $this->type)));
        $this->render($views[$this->type], compact('banners'));
    }
}
