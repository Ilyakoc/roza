<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 15.09.11
 * Time: 11:55
 * To change this template use File | Settings | File Templates.
 */

Yii::import('ext.ContentDecorator.plugins.*');

class ContentDecorator
{
    public static $plugins = array(
        'infoblock',
        'oneimage',
        'feedback',
        'simplegallery',
        'gallery',
        'comments',
        'gismap',
        'jsinsert'
    );

    /**
     * @var \CModel|null
     */
    public $model = null;

    public static function decorate(& $model, $attribute='text')
    {
        $instance = new self($model);
        $instance->decorateAll($attribute);
    }

    public function __construct(CModel & $model)
    {
        $this->model = $model;
    }

    public function decorateAll($attribute='text')
    {
        foreach (self::$plugins as $plugin) {
            $decorator = $this->loadPlugin($plugin);

            if (!$decorator)
                continue;

            $decorator->processModel($this->model, $attribute);
        }
    }

    /**
     * @param $plugin
     * @return bool|PluginDecorator
     */
    private function  loadPlugin($plugin)
    {
        $class = ucfirst($plugin). 'PluginDecorator';

        $file = dirname(__FILE__).DS.'plugins'.DS.$class.'.php';

        if (!is_file($file))
            throw new CException('Decorator file not found');

        if (!class_exists($class))
            throw new CException('Decorator class not exists');

        return new $class;
    }
}

abstract class PluginDecorator
{
    protected $point = null;
    
    abstract function processModel($model);

    protected function checkPoint($text)
    {
        $search = '#<(p|div)[^>]*>.*('. $this->point .').*</\1>#';

        if (preg_match($search, $text, $values)) {
            $this->point = $values[0];
        } else
            return false;

        return true;
    }

    protected function replace($subject, $replace)
    {
        return str_replace($this->point, $replace, $subject);
    }

    protected function includeJs()
    {
        $cs = Yii::app()->clientScript;
        
        $cs->registerCoreScript('jquery');
    }
}
