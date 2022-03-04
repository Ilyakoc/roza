<div id="uploadFilesForm" class="uploadForm">
    <label>Загрузка файлов</label>
    <?php echo $form->fileField($model, 'file[]'); ?>
    <a class="add js-link">Добавить еще</a>
    <div class="added-files"></div>

    <script type="text/javascript">
        $(function() {
            var context = $('#uploadFilesForm');
            var add     = $('.add', $(context));
            var input   = $(add).prev();

            var input_clone = input.clone();
            var rlink = $('<a></a>').text('Удалить').addClass('js-link').click(function() {
                $(this).parent().remove();
            });

            var div = $('<div></div>').addClass('ufile');
            $(input_clone).appendTo(div);
            $(rlink).appendTo(div);

            $(add).click(function() {
                var new_obj = $(div).clone(true);
                $('.added-files', $(context)).append($(new_obj));
            });
        });
    </script>
</div>
