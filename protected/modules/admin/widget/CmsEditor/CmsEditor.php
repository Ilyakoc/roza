<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 21.10.11
 * Time: 12:53
 * To change this template use File | Settings | File Templates.
 */
 
class CmsEditor extends CWidget
{
    public $model;
    public $attribute   = null;
    public $full        = true;
    public $htmlOptions = array();

    private $_assetsUrl;

    public function run()
    {
        if ($this->model instanceof CModel == false)
            throw new CException('Model not valid');

        $class = $this->full ? 'mceEditor' : 'mceEditor-lite';

        if (isset($this->htmlOptions['class']))
            $class .= ' '.$this->htmlOptions['class'];

        echo CHtml::activeTextArea($this->model, $this->attribute, array('class'=>$class));

        $this->registerJs();
        $this->submitScript();
    }

    private function registerJs()
    {
        $assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('admin.widget.CmsEditor.assets'));
        $cs        = Yii::app()->clientScript;

        $cs->registerScriptFile($assetsUrl. '/js/tiny_mce/tiny_mce.js', CClientScript::POS_BEGIN);
        $cs->registerCssFile($assetsUrl. '/css/editor-ui.css');

        $type = $this->full ? 'full' : 'lite';
        $id   = 'tiny_mce_init_'. $type;

        if (!$cs->isScriptRegistered($id)) {
            $cs->registerScript($id, $this->initScript($type, $assetsUrl), CClientScript::POS_BEGIN);
        }
    }

    private function initScript($type, $assets)
    {
        $file = dirname(__FILE__).DS.'types'.DS.$type.'.js';
        return $this->renderFile($file, array('assets'=>$assets, 'gismapDialog'=>Yii::app()->createUrl('admin/default/gisMapDialog')), true);
    }

    /**
     * Method for save editor content
     * Deprecated
     * @return void
     */
    private function submitScript()
    {
        $field_name = CHtml::resolveName($this->model, $this->attribute);
        $field_id   = CHtml::getIdByName($field_name);

        $js = "$('#$field_id').parents('form').submit(function(){tinyMCE.get('$field_id').save();})";

        Yii::app()->clientScript->registerScript('tiny_mce_submit_'. $field_id, $js);
    }
}
