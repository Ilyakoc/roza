<?php $this->pageTitle = 'Страницы - '. $this->appName; ?>

<h1>Страницы</h1>

<table class="adminList">
    <thead>
    <tr>
        <th>Заголовок</th>
        <th>Псевдоним</th>
        <th>Главная?</th>
        <th>Активна?</th>
        <th>Создана</th>
        <!--th>Изменена</th-->
    </tr>
    </thead>

    <tbody>
    <?php foreach($pages as $page): ?>
    <tr>
        <td class="title"><?php echo CHtml::link($page->title, array('page/update', 'id'=>$page->id)); ?></td>
        <td><?php echo $page->alias; ?></td>
        <td><?php echo $page->mainpage ? 'Да' : 'Нет' ; ?></td>
        <td><?php echo $page->publish ? 'Да' : 'Нет' ; ?></td>
        <td class="date"><?php echo $page->created; ?></td>
        <!--td class="date"><?php echo $page->modified; ?></td-->
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

