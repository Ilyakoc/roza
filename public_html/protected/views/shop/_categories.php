<ul class="category-list">
    <?php if (!isset($product)) { ?>
    <li <?php if (!isset($category_id)) echo 'class="active"'; ?>>
        <?php echo CHtml::link('Новинки', array('shop/index'), array('class'=>'js-link')); ?>
    </li>
    <?php } ?>

    <?php foreach($categories as $category): ?>
    <li <?php if (isset($category_id) && $category_id == $category->id) echo 'class="active"'; ?>>
        <?php echo CHtml::link($category->title, array('shop/category', 'id'=>$category->id), array('class'=>'js-link')); ?>
    </li>
    <?php endforeach; ?>
</ul>
<?php if (!isset($product)) { ?>
<script type="text/javascript">
    $(function(){
        var cats = $('#category-list-module a');
        var lis  = $('#category-list-module li');

        $(cats).click(function(e){
            var parent = $(this).parent();
            e.preventDefault();

            if ($(parent).hasClass('active'))
                return;

            $.getJSON($(this).attr('href'), function(data) {
                $('#product-list-module').html(data.content);
                $('#content h1').text(data.contentTitle);

                if (!$('#category-description').length) {
                    $('<div></div>').attr({'class':'category-description', 'id':'category-description'}).insertBefore('#product-list-module');
                }
                $('#category-description').html(data.description);

                document.title = data.title;
                $(lis).removeClass('active');
                $(parent).addClass('active');
            });
        });
    });
</script>
<?php } ?>
