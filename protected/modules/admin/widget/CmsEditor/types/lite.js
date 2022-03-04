tinyMCE.init({
    mode : "textareas",
    theme : "advanced",
    editor_selector : "mceEditor-lite",
    plugins : "paste",
    language : "ru",
    relative_urls : false,
    theme_advanced_buttons1 : "bold,italic,|,link,unlink,removeformat,|,pasteword,|,code",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    content_css: '<?php echo $assets; ?>/css/editor.css'
});
