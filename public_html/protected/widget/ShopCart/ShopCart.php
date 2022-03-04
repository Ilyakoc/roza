<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 09.09.11
 * Time: 17:18
 * To change this template use File | Settings | File Templates.
 */

class ShopCart extends CWidget
{
    public $summary  = false;
    public $products = false;
    public $view = 'default';

    public function run()
    {
        $cart = CmsCart::getInstance()->cartInfo();
/*
        if ($this->summary)
            $this->render('_summary', compact('cart'));

        elseif ($this->products)
            $this->render('_products', compact('cart'));

        else {*/
            CmsHtml::js('/js/jquery-impromptu.3.2.min.js');
            CmsHtml::js('/js/jquery.debounce-1.0.5.js');
            CmsHtml::js('/js/shop.js');

            $this->render($this->view, compact('cart'));
       // }
    }
}
