<?php CmsHtml::fancybox(); ?>

<h1>Отзывы</h1>

<div>
	<?php $this->renderPartial('_form', compact('model')); ?>
</div>


<div id="question-list" class="question-list">
	<?php
	$this->widget('zii.widgets.CListView', array(
	                                            'dataProvider'=>$dataProvider,
	                                            'itemView'=>'_feedback',   // refers to the partial view named '_post'
	                                            'template'=>'{items}{pager}',
	                                            'pager'=>array(
		                                            'header'=>'',
		                                            'firstPageLabel'=>'<<',
		                                            'prevPageLabel'=>'<',
		                                            'nextPageLabel'=>'>',
		                                            'lastPageLabel'=>'>>',
	                                            )
	                                       ));
	?>
</div>

<script type="text/javascript">
    $(function() {

        $('#add-question a').click(function() {
            $.fancybox({
                'href': '#question-form-div',
                'scrolling': 'no',
                'titleShow': false,
                'onComplete': function(a, b, c) {
                    $('#fancybox-wrap').addClass('formBox');
                }
            });
        });
    });

    function submitForm(form, hasError) {
        if (!hasError) {
            $.post($(form).attr('action'), $(form).serialize(), function(data) {
                if (data == 'ok')
                    $('#question-form-div').html('<h2>Ваш отзыв отправлен</h2>');
                else
                    $('#question-form-div').html('<h2>При отправке отзыва возникла ошибка</h2>');
            });
        }
    }
</script>
