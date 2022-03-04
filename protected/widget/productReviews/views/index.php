<?php /**
 * File: index.php
 * User: Mobyman
 * Date: 10.04.13
 * Time: 12:56
 * @var Product $product
 */ ?>
<?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/review.css'); ?>

    <div class="reviews">
        <span id="add-review">
            <a>Написать отзыв</a>
        </span>
    </div>
    <div class="clr"></div>

<?php
$this->render('_product_review_form', compact('model', 'product'));
$this->render('_product_review_js');
?>
    <ul class="reviews">
        <?php $i = 1; foreach($product->reviews as $review): ?>
            <li <?php if($i > 1) {echo 'class="cut hide"'; } ?>>
                <span class="username"><?php echo $review->username; ?></span>
                <span class="star-view star-<?php echo $review->mark; ?>"></span>
                <div class="text">
                    <?php echo $review->text; ?>
                </div>
            </li>
        <?php $i++; endforeach; ?>
        <?php if($i > 2) { echo CHtml::link('Развернуть всё', 'javascript:;', array('class' => 'cutlink')); } ?>
    </ul>
<div class="clr"></div>
