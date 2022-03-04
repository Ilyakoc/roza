<ul id="uploadedFiles" class="uploadedList">
    <?php foreach($items as $item) $this->render('_item_file', compact('item')); ?>
</ul>

<script type="text/javascript">
    $(function(){
        $('.ajax-link', $('#uploadedFiles')).live('click', function(e) {
            e.preventDefault();

            var self = $(this);
            var url  = $(this).attr('href');
            $.get(url, null, function(data){
                if (data == 'ok')
                    $(self).parent().remove();
            });
        });
    });

    function insertLink(dir, fname) {
        var ed = tinyMCE.activeEditor, bm = false, url;
        url = dir + fname;

        if (tinyMCE.isIE) {
            ed.focus();
        }
        if (ed.selection.isCollapsed()) {
            ed.selection.setContent('<a href="'+url+'">'+fname+'</a>');
        } else {
            ed.execCommand('mceInsertLink', false, url);
        }
    }
</script>
 
