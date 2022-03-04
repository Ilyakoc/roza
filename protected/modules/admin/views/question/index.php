<h1>Вопрос-ответ</h1>

<?php if (count($list)): ?>
<script type="text/javascript">
 $(function(){
    $('#faq').on('click', '.user', function(){
       $("table#faq").find(".details[data-item='" + $(this).data('item') + "']").toggle();
    });
    $('#faq').on('blur', '.question', function(){
       t = $(this);
       $.ajax({
              type: "POST",
              url: "<?php echo Yii::app()->createUrl("/admin/question/ajax"); ?>",
              data: {item: $(this).data('item'), action: "question", text: $(t).val()},
              dataType: "json",
              success: function(data) {
                  $(t).val(data.text);
              }
        });
    });
    $('#faq').on('blur', '.answer', function(){
       t = $(this);
       $.ajax({
              type: "POST",
              url: "<?php echo Yii::app()->createUrl("/admin/question/ajax"); ?>",
              data: {item: $(this).data('item'), action: "answer", text: $(t).val()},
              dataType: "json",
              success: function(data) {
                  $(t).val(data.text);
                  $('#questioncount').text(data.count);
              }
        });
    });
 });
</script>
<table id="faq">
    <tr class="head">
        <td class="number">№</td>
        <td class="date">Дата</td>
        <td>ФИО</td>
        <td style="width:50px;">Действия</td>
    </tr>
    <?php foreach($list as $item): ?>
    <tr id="question-<?php echo $item->id; ?>" class="row<?php echo $item->id % 2 ? 0 : 1; ?>">
        <td><?php echo $item->id; ?></td>
        <td><?php echo date("d.m.Y, H:i", strtotime($item->created)); ?></td>
        <td class="title"><?php echo CHtml::link($item->username, "javascript:;", array('class' => 'user', 'data-item' => $item->id)); ?></td>
        <td class="actions"><?php echo CHtml::ajaxLink('Удалить', $this->createUrl('question/delete', array('id'=>$item->id)),
            array(
                'type'=>'post',
                'data'=>array('ajax'=>1),
                'success'=>'function() {$("#question-'. $item->id .'").remove(); $("#faq").find(".details[data-item=\''. $item->id  . '\']").remove();}'
            )); ?>
        </td>
    </tr>
    <tr class="details" data-item="<?php echo $item->id; ?>">
        <td colspan="5">
            <label>
                <span>Вопрос</span><br>
                <textarea class="question" data-item="<?php echo $item->id?>"><?php echo $item->question; ?></textarea>
            </label>
            <div class="clr"></div>
            <label>
                <span>Ответ</span><br>
                <textarea class="answer" data-item="<?php echo $item->id?>"><?php echo $item->answer; ?></textarea>
            </label>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p>Нет вопросов</p>
<?php endif; ?>


