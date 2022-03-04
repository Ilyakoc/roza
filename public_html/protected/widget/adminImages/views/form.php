<div id="uploadImagesForm" class="uploadForm">
    <label>Загрузка фото</label>
    <?php echo $form->fileField($model, 'image[]'); ?>
    <a id="add_image" class="js-link">Добавить еще</a>
    <div class="added-files"></div>

    <script type="text/javascript">
        $(function() {
            var div = $('<div></div>').addClass('ufile');
            var input = $('#add_image').prev();

            var input_clone = input.clone();
            var rlink = $('<a></a>').text('Удалить').addClass('js-link').click(function() {
                $(this).parent().remove();
            });

            $(input_clone).appendTo(div);
            $(rlink).appendTo(div);

            $('#add_image').click(function() {
                var new_obj = $(div).clone(true);
                $('.added-files', $('#uploadImagesForm')).append($(new_obj));
            });
        });
    </script>
</div>
