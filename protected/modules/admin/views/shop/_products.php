<script type="text/javascript">
$(function() {
    $("#product-list").sortable({
        cursor: "move",
        stop: function(event, ui) {
            var order = $(this).sortable('toArray');
            console.log(order);
            $.ajax({
                    url: '/cp/default/shoporder',
                    type: 'post',
                    data: {products: order, cat_id: <?php echo isset($products[0]) ? $products[0]->category_id : 0; ?>},
                    success: function(data) {
                        console.log(data);
                    }
                });
        }
    });
    $("#site-menu").disableSelection();
});
</script>
<div id="product-list-module">
    <?php if (count($products)): ?>
    <ul id="product-list" class="product-list">
        <?php foreach($products as $product): ?>
        <li id="item_<?php echo $product->id ?>">
            <div class="product">
                <div class="img">
                    <a href="<?php echo $this->createUrl('productUpdate', array('id'=>$product->id)); ?>"><img src="<?php echo $product->mainImg; ?>" alt="" /></a>
                </div>
                <p class="title"><?php echo Chtml::link($product->title, array('productUpdate', 'id'=>$product->id)); ?></p>
                <p class="price"><?php echo $product->price; ?> руб.</p>
            </div>  
        </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
    <p>Нет товаров</p>
    <?php endif; ?>
</div>
