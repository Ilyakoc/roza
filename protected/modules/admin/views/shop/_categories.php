<?php if (isset(Yii::app()->params['subcategories']) || !isset($model)): ?>
<div id="category-list-module">
    <ul id="category-list" class="category-list">
        <?php foreach($categories as $c): ?>
        <li id="shop-category-<?php echo $c->id ?>"><?php echo CHtml::link($c->title, array('category', 'id'=>$c->id)); ?></li>
        <?php endforeach; ?>
        <li class="add">
            <?php
            if (!isset($model))
                echo CHtml::link('Новая категория', array('shop/categoryCreate'));
            else
                echo CHtml::link('Новая категория', array('shop/categoryCreate', 'parent_id'=>$model->id));

            echo CHtml::link('Сортировать категории', array('shop/categorySort'), ['style' => 'margin-left: 15px;']);
            echo CHtml::link('Перенести товары', array('shop/productCategoryChange'), ['style' => 'margin-left: 15px;']);
            ?>
        </li>
    </ul>
</div>
<?php endif; ?>

<script type="text/javascript">
    $('#category-list').sortable({
        items: "li:not(.add)",
        stop: function(event, ui) {
            var order = $(this).sortable('serialize');
            $.post('<?php echo $this->createUrl('shop/categoryOrder'); ?>', order);
        }
    });
    $("#category-list").disableSelection();
</script>

<div class="category-buttons" style="margin-bottom: 20px;">
    <?php

    $route = array('shop/productCreate');
    if (isset($model))
        $route['category_id'] = $model->id;

    ?>
    <?php echo CHtml::link('Новый товар', $route, array('class'=>'add default-button')); ?> &nbsp;
    <?php if (isset($model)) echo CHtml::link('Редактировать категорию', array('shop/categoryUpdate', 'id'=>$model->id), array('class'=>'add default-button')); ?>
</div>
