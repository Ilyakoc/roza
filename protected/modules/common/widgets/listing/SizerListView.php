<?php
/**
 * @link http://www.elisdn.ru/blog/43/various-pagesize-for-clistview
 */
namespace common\widgets\listing;

\Yii::import('zii.widgets.CListView');

class SizerListView extends \CListView
{
    /**
     * @var string GET attribute
     */
    public $sizerAttribute = 'size';
 
    /**
     * @var array items per page sizes variants
     */
    public $sizerVariants = array(50, 100, 150, 200);
 
    /**
     * @var string CSS class of sorter element
     */
    public $sizerCssClass = 'sizer';
 
    /**
     * @var string the text shown before sizer links. Defaults to empty.
     */
    public $sizerHeader = 'Show by: ';
 
    /**
     * @var string the text shown after sizer links. Defaults to empty.
     */
    public $sizerFooter = '';
 
    public function renderSizer()
    {
        $pageVar = $this->dataProvider->getPagination()->pageVar;    
        $pageSize = $this->dataProvider->getPagination()->pageSize;    
 
        $links = array();       
        foreach ($this->sizerVariants as $count)
        {
            $params = array_replace($_GET, array($this->sizerAttribute => $count));
 
            if (isset($params[$pageVar])) 
                unset($params[$pageVar]);
 
            if ($count == $pageSize)
                $links[] = $count;
            else            
                $links[] = \CHtml::link($count, \Yii::app()->controller->createUrl('', $params));
        }        
        echo \CHtml::tag('div', array('class'=>$this->sizerCssClass), $this->sizerHeader . '<ul><li>'.implode('</li><li>', $links).'</li></ul>');
        echo $this->sizerFooter;
    }
}