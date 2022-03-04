<?php
/**
 * File: ProductReviews.php
 * User: Mobyman
 * Date: 10.04.13
 * Time: 12:54
 */

class ProductReviews extends CWidget {

    public $product_id;

    public function run(){

        $model = new ProductReview();

        $product =  Product::model()->with('reviews:published')->findByPk($this->product_id);
        $this->render('index', compact('model', 'product'));
    }

}