<ul id="uploadedFiles" class="uploadedList">
    <?php foreach($files as $item) : ?>
    <li>
        <a class="link" onclick="insertLink('/files/<?php echo $item->model; ?>/', '<?php echo $item->filename; ?>')"><?php echo $item->filename; ?></a>
        ( <?php echo CHtml::link('x', array('default/removeFile', 'id'=>$item->id), array('class'=>'ajax-link')); ?> )
    </li>
    <?php endforeach; ?>
</ul>
<div class="clr"></div>

