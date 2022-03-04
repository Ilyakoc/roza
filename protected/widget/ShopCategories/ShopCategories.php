<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 13.01.12
 * Time: 14:37
 * To change this template use File | Settings | File Templates.
 */

class ShopCategories extends CWidget
{
    public $listClass = 'shop-menu';
    public $disabled=array();

    private $_categories;

    public function run()
    {
        //$categories = Category::model()->findAll(array('order'=>'ordering'));
        //Category::model()->refresh();
        $this->_categories = Category::model()->findAll(array('order'=>'ordering, lft'));

        //$items = CmsCore::prepareTreeMenu($categories);
        $items = $this->prepareTree();

        $this->render('default', compact('items'));
    }

    private function prepareTree($level = 1, $parent = null)
    {
        $items = array();

        foreach($this->_categories as $cat) {
            /* @var Menu|NestedSetBehavior $cat */

            if ($cat->level!=$level || $cat->hide_menu)
                continue;

            if ($parent && !$cat->isDescendantOf($parent))
                continue;

			$disabled=in_array($cat->id, $this->disabled);
			
			if($disabled) $url='javascript:;';
			else $url=array('shop/category', 'id'=>$cat->id);
			
            $item = array(
                'label'=>$cat->title,
                'url'=>$url
            );
            
            if($disabled) {
            	$url=Yii::app()->getController()->createUrl('shop/category', ['id'=>$cat->id]);
            	$item['template']='<span data-url="'.$url.'">'.$cat->title.'</span>';
            }

            if (!$cat->isLeaf()) {
                $item['items'] = $this->prepareTree($cat->level+1, $cat);
            }

            $items[] = $item;
        }
        return $items;
    }
}
