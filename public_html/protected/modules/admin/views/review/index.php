<?php
    /**
     * File: index.php
     * User: Mobyman
     * Date: 28.01.13
     * Time: 12:26
     */
?>
    <style type="text/css">
        span.star-view {background: url(<?php echo Yii::app()->baseUrl; ?>/images/marks/star.png); height:16px; display: inline-block;vertical-align: top;}
        .star-1 {width:18px;}
        .star-2 {width:36px;}
        .star-3 {width:54px;}
        .star-4 {width:72px;}
        .star-5 {width:90px;}
    </style>
    <script type="text/javascript">
        $(function() {
            $("#reviews").on('click', '.mark', function(){
                t = $(this);
                $.ajax({
                    type: "POST",
                    url: "<?php echo Yii::app()->createUrl("/admin/review/ajax"); ?>",
                    data: {item: $(this).data('item'), action: "publish"},
                    dataType: "json",
                    success: function(data) {
                        if(!data.status) {
                            $(t).removeClass('unmarked');
                        } else {
                            $(t).addClass('unmarked');
                        }
                    }
                });
            })
        });
    </script>
    <h1>Отзывы</h1>
<?php if(!$model): ?>
    Отзывы пока отсутствуют...
<?php else: ?>
    <table id="reviews">

        <tr class="head">
            <td style="border-right:none; width:35px;">Имя</td>
            <td style="border:none;width:305px;">Текст</td>
            <td>Оценка</td>
            <td></td>
            <td></td>
        </tr>
        <?php foreach($model as $item): ?>
            <tr class="order" data-item="<?php echo $item->id; ?>">
                <td><span title="<?php echo long2ip($item->ip); ?>"><?php echo $item->username; ?></span></td>
                <td><?php echo $item->text; ?></td>
                <td><span class="star-view star-<?php echo $item->mark; ?>"></span></td>
                <td><div class="mark <?php echo !$item->published ? 'marked' : 'unmarked'; ?>" data-item="<?php echo $item->id; ?>"></div></td>
                <td><?php echo CHtml::link('»', array('/shop/product', 'id' => $item->product_id), array('style' => 'text-decoration:none;', 'target' => '_blank')); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php $this->widget('CLinkPager', array(
        'pages' => $pages,
    )); ?>

<?php endif; ?>