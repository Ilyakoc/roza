<div class="photo_box" id="image-<?php echo $item->id; ?>">
    <div class="img">
        <?php echo CHtml::link('', array('default/removeImage', 'id'=>$item->id), array('class'=>'remove-icon')); ?>
        <img src="<?php echo $item->tmbUrl; ?>" alt="" />
    </div>

    <div class="buttons clear_fix">
        <a class="js-link left" onclick="openDialog(<?php echo $item->id; ?>);">изменить</a>
        <a class="js-link right" onclick="insertImage(this)">вставить</a>
    </div>

    <div style="display: none;">
        <div id="uplImg-<?php echo $item->id; ?>">
            <div class="form">
                <div class="row">
                    <img src="<?php echo $item->url; ?>" alt="" width="300" />
                </div>

                <div class="row">
                    <div id="status-<?php echo $item->id; ?>" class="right"></div>
                    <label for="desc-<?php echo $item->id; ?>">Описание</label>
                    <textarea id="desc-<?php echo $item->id; ?>" name="desc-<?php echo $item->id; ?>"><?php echo $item->description; ?></textarea>
                </div>

                <div class="left">
                    <input type="button" class="default-button" value="Сохранить описание" onclick="saveImageDesc(<?php echo $item->id; ?>)" />
                </div>
                <div class="right with-default-button">
                    <a class="link" href="<?php echo Yii::app()->createUrl('admin/default/removeImage', array('id'=>$item->id)); ?>">Удалить фото</a>
                </div>
                <div class="clr"></div>
            </div>
        </div>
    </div>
</div>

