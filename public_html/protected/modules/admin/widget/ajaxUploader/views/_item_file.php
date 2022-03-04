<li><a class="link" onclick="insertLink('/files/<?php echo $item->model; ?>/', '<?php echo $item->filename; ?>')"><?php echo $item->filename; ?></a>
    ( <?php echo CHtml::link('x', array('ajax/removeFile', 'id'=>$item->id), array('class'=>'ajax-link')); ?> )</li>
