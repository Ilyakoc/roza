<?

$baseUrl = $this->module->assetsUrl;

?>

<script>

function toggleCheckBox(self)  {
	$('.m_default:checkbox').attr('checked', false);
    $(self).attr('checked', true);
}

function toggleHidden(self) {
    $.ajax({
        url: "<?php echo Yii::app()->createUrl('devadmin/menu/toggleHidden'); ?>",
        type: 'get',
        data: {id: $(self).parents('tr').data('menuid')},
        success: function(data) {

            if ($(self).attr('checked') == 'checked') 
                $(self).attr('checked', false);
            else
                $(self).attr('checked', true);
        }
    });
}

function toggleDefault(self) {
    $.ajax({
        url: "<?php echo Yii::app()->createUrl('devadmin/menu/toggleDefault'); ?>",
        type: 'get',
        data: {id: $(self).parents('tr').data('menuid')},
        success: function(data) {
        	toggleCheckBox($(self));
        }
    });
}

function changeName(self) {
    var value = $(self).text();
    $(self).html("<input class='title' type='text' value='" + value + "' />");
    $(self).find(".title").focus();
    $(self).attr("onClick", '');
    $(self).find("input").attr("onBlur", "saveName(this, '" + value + "');");
}

function saveName(self, value) {
    $.ajax({
            url: "<?php echo Yii::app()->createUrl('devadmin/menu/changeName'); ?>",
            type: 'get',
            data: {id: $(self).parents("tr").data('menuid'), newname: $(self).val()},
            success: function(data) {
                $(self).parent().text($(self).val()).attr('onClick', 'changeName(this);');
                $(self).remove();
            },
            error: function(data) {
                $(self).parent().text(value).attr('onClick', 'changeName(this);');
                $(self).remove(); 
            }
        });
}




</script>



<table>
<tr>

	<th style="width:400px;">Заголовок</th>
	<th><img title="Скрыть"src="<?php echo $baseUrl ?>/images/visible.png"></th>
	<th><img title="Пункт по умолчанию" src="<?php echo $baseUrl ?>/images/default.png"?></th>
</tr>


<?php foreach ($model as $item): ?>
<tr data-menuid="<?php echo $item->id ?>" class="menuitems">
<td style="width:400px;"><span class="title" onClick="changeName(this);"><?php echo $item->title; ?></span></td>
<td><?php echo CHtml::checkBox('hidden', $item->hidden, array('onClick' => 'toggleHidden(this); return false;')); ?></td>
<td><?php echo CHtml::checkBox('default', $item->default,  array('onClick' => 'toggleDefault(this); return false;', 'class' => 'm_default')); ?></td>
</tr>
<?php endforeach; ?>
</table>
<br>
<div class="row"><i>*Клик по пункту, чтобы переименовать.</i></div>
