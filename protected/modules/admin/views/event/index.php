<?php $this->pageTitle = 'Новости - '. $this->appName; ?>

<h1>Новости</h1>

<div style="margin-bottom: 15px;">
    <?php echo CHtml::link('Добавить', array('create'), array('class'=>'default-button')); ?>
</div>

<?php if (!count($events)): ?>
<p>Нет новостей</p>
<?php else: ?>
<table class="adminList">
    <thead>
    <tr>
        <th>Название</th>
        <th style="width: 1%">Тип</th>
        <th style="width: 1%"></th>
        <th style="width: 1%"></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $types=array('news' => 'Новости', 'article' => 'Статья', 'wiki' => 'Энциклопедия');
    foreach($events as $event): ?>
    <tr id="event-<?php echo $event->id; ?>" class="row<?php echo $event->id % 2 ? 0 : 1; ?>">
        <td class="title"><?php echo CHtml::link($event->title, array('event/update', 'id'=>$event->id)); ?></td>
        <td><?php echo $types[$event->type]; ?></td>
        <td><?php echo $event->date; ?></td>
        <td><?php echo CHtml::ajaxLink('удалить', $this->createUrl('event/delete', array('id'=>$event->id)),
            array(
                'type'=>'post',
                'data'=>array('ajax'=>1),
                'success'=>'function() {$("#event-'. $event->id .'").remove();}'
            )); ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
