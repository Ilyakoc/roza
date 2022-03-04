<?php
/**
 * File: ReviewController.php
 * User: Mobyman
 * Date: 10.04.13
 * Time: 15:51
 */

class ReviewController extends Controller {

    public function actionIndex(){



        $model = new ProductReview();

        if (isset($_POST['ProductReview'])) {
            $model->attributes = $_POST['ProductReview'];
            $productExists = Product::model()->findByPk($model->product_id);
            if($productExists) {
                if ($model->save())
                    echo 'ok';
                else
                    echo 'error';
            }

            Yii::app()->end();
        }
    }
}