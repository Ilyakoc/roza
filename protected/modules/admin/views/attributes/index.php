<? $this->breadcrumbs = array('Атрибуты товара'=>array('attributes/index'));?>

<h1>Атрибуты товара</h1>

<a class="btn btn-primary" href="<?php echo $this->createUrl('attributes/add')?>">Новый атрибут</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'eav-attribute-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        'id',
        'name',
        // 'type',
        // 'fixed',
        // 'sort',
        array(
            'class'=>'CButtonColumn',
        ),
    ),
)); ?>


