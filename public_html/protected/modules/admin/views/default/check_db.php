<?php $this->pageTitle = 'Обновление базы данных - '. $this->appName; ?>

<h1>Обновление базы данных</h1>

<h4>Основные запросы</h4>
<ol>
    <?php foreach($query_list['general'] as $query): ?>
    <li><?php echo $query; ?></li>
    <?php endforeach; ?>
</ol>
<?php if (!$query_list['general']) echo '<p>нет</p>' ?>

<h4>Удаление таблиц</h4>
<ol>
    <?php foreach($query_list['after'] as $query): ?>
    <li><?php echo $query; ?></li>
    <?php endforeach; ?>
</ol>
<?php if (!$query_list['after']) echo '<p>нет</p>' ?>

<?php if ($query_list['after'] || $query_list['general']): ?>
    <?php echo CHtml::link('Подтвердить', array('checkdb'), array('id'=>'submit-check')); ?>
<?php endif; ?>

<script type="text/javascript">
    $(function() {
        $('#submit-check').click(function(e) {
            e.preventDefault();

            $.get($(this).attr('href'), function(data) {
                window.location.reload();
            });
        });
    });
</script>
