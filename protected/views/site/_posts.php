<?php foreach($posts as $post): ?>
    <div class="post">
        <?php if (Yii::app()->settings->get('cms_settings', 'blog_show_created') == true): ?>
        <div class="created"><?php echo $post->date; ?></div>
        <?php endif; ?>
        <h2><?php echo CHtml::link($post->title, array('site/page', 'id'=>$post->id)); ?></h2>
        <div class="intro">
            <?php echo $post->getIntro(); ?>
        </div>
    </div>
<?php endforeach; ?>

<?php $this->widget('CLinkPager', array(
    'header'=>'Страницы: ',
    'pages'=>$pages,
    'nextPageLabel'=>'&gt;',
    'prevPageLabel'=>'&lt;',
    'cssFile'=>false,
    'htmlOptions'=>array('class'=>'news-pager')
)); ?>
