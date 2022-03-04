<?php if ($this->show_title): ?>
<h2>Новости</h2>
<?php endif; ?>

<dl class="events">
    <?php foreach($events as $event): ?>
    <dt><?php echo $event->date; ?></dt>
    <dd><?php echo CHtml::link($event->title, array('site/event', 'id'=>$event->id)); ?></dd>
    <?php endforeach; ?>
</dl>

<?php if ($show_all) echo CHtml::link('Все новости', array('site/events'), array('class'=>'all_events')); ?>
