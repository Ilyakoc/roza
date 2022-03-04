    <?php if ($this->show_title): ?>
    <h2>Новости</h2>
    <?php endif; ?>
    
    <ul class="events">
        <?php foreach($events as $event): ?>
        <li>
            <p class="created"><?php echo $event->date; ?></p>
            <?php echo CHtml::link($event->title, array('site/event', 'id'=>$event->id), array('class'=>'head')); ?>
            <div class="intro"><?php echo $event->getIntro() ?></div>
        </li>
        <?php endforeach; ?>
    </ul>
    
    <?php if ($show_all) echo CHtml::link('Все новости', array('site/events'), array('class'=>'all_events')); ?>
