<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 24.07.12
 * Time: 12:35
 * To change this template use File | Settings | File Templates.
 */
class RelatedProducts extends CWidget
{
    public $_id;

    public function run()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'category_id = ? AND id <> ?';
        $criteria->params = array('32', $this->_id);
        $criteria->limit = 6;

        $products = Product::model()->findAll($criteria);

        if ($products)
            $this->render('default', compact('products'));
    }
}
