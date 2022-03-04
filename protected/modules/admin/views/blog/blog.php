<?php $this->pageTitle = 'Блог '. $model->title .' - '. $this->appName; ?>

<div class="left">
    <h1><?php echo $model->title; ?></h1>
</div>
<div class="right">
    <?php echo CHtml::link('Изменить', array('update', 'id'=>$model->id)); ?>
</div>
<div class="clr"></div>

<div style="margin-bottom: 15px;">
    <?php echo CHtml::link('Добавить статью', array('page/create', 'blog_id'=>$model->id), array('class'=>'default-button')); ?>
</div>

<?php if ($model->posts) : ?>
<table class="adminList">
    <tr><td></td><td width="1%"></td></tr>
    <?php foreach($model->posts as $id=>$post): ?>
    <tr class="row<?php echo $id % 2 ? 0 : 1; ?>">
        <td><?php echo CHtml::link($post->title, array('page/update', 'id'=>$post->id)); ?></td>
        <td><?php echo CHtml::link("Удалить", array('page/delete', 'id' => $post->id)); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
    <p>Нет статей!</p>
<?php endif; ?>
