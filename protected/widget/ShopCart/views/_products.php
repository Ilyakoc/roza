<?php if (count($cart->products)): ?>
    <ul class="list">
        <?php foreach($cart->products as $id=>$product): ?>
        <li id="cart-product-<?php echo $id; ?>">
            <table class="list-item">
            <tr>
                <td class="img">
                    <a href="<?php echo Yii::app()->createUrl('shop/product', array('id'=>$product->id)); ?>">
                        <img src="<?php echo $product->tmbImg; ?>" width="45" alt="" /></a>
                </td>
                <td class="info">
                    <?php echo CHtml::link($product->title, array('shop/product', 'id'=>$product->id)); ?>
                    <span class="price"><?php echo $product->price; ?></span> руб.
                </td>
                <td class="count">
                    <input name="count[<?php echo $id; ?>]" type="text" size="2" maxlength="3"
                           value="<?php echo $cart->self->count($id); ?>" /> шт.
                </td>
            </tr>
            </table>
        </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
