tiny_togglePanel = function(toolbarName, buttonName) {
    var eds = tinymce.editors;
    tinymce.each(eds, function(ed) {
        var cm = ed.controlManager;

        var button     = cm.get(cm.prefix + buttonName);
        if (!button) return;
        var tablePanel = cm.get(cm.prefix + toolbarName);

        if (tablePanel.isDisabled()) {
            tablePanel.setDisabled(false);
            button.setActive(true);
        } else {
            tablePanel.setDisabled(true);
            button.setActive(false);
        }
    });
};

tinymce.init({
    editor_selector : "mceEditor",
    plugins : "paste, table, -gismap", //-cmsbuttons
    mode : "textareas",
    theme : "advanced",
    language : "ru",
    relative_urls : false,
    paste_remove_styles: true,
    fix_table_elements : true,
    content_css: '<?php echo $assets; ?>/css/editor.css',
    theme_advanced_buttons1 : "tablesettings,formatselect,styleselect,bold,italic,justifyleft,justifycenter,justifyfull,|,strikethrough,bullist,numlist,link,unlink,image,hr,removeformat,|,pastetext,pasteword,|,code",
    theme_advanced_buttons2 : "tablecontrols,|,delete_table",
    theme_advanced_buttons3 : "cms_form_button,cms_gallery_1,cms_gallery_2,cms_comments,gismap", //2gismap,
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_blockformats : "p,h3,h4,div",
    theme_advanced_resizing : true,
    theme_advanced_resize_horizontal : false,
    table_styles : "Информация=infoTbl;Обычная=stdTbl",
    extended_valid_elements : "iframe[name|src|framespacing|border|frameborder|scrolling|title|height|width],"
                            + "object[declare|classid|codebase|data|type|codetype|archive|standby|height|width|usemap|name|tabindex|align|border|hspace|vspace],"
                            + "embed[param|src|type|width|height|flashvars|wmode|allowscriptaccess|allowfullscreen], param[name|value]",
    media_strict: false,
    extended_valid_elements: '*[*]',
    valid_elements: '*[*]',
    style_formats : [
        { title: "Важная информация", block: "div", "classes": "user_info_block", wrapper: true}
    ],
    oninit: function(){
        tiny_togglePanel('toolbar2', 'tablesettings');
    },
    setup : function(ed) {
        ed.addButton('tablesettings', {
            title : 'Таблицы',
            onclick : function(){
                tiny_togglePanel('toolbar2', 'tablesettings');
            }
        });

        ed.addButton("cms_form_button", {
            title : 'Форма обратной связи',
            onclick : function() {
                var call_block = "{form_feedback}";
                var content = ed.getContent();
                if (content.indexOf(call_block) < 0) {
                    ed.focus();
                    ed.selection.setContent(call_block);
                } else {
                    ed.windowManager.alert("Вы уже вставили форму");
                }
            }
        });

        ed.addButton("cms_gallery_1", {
            title   : "Галерея (тип 1)",
            onclick : function() {
                var call_block = "{simple_gallery}";
                var content = ed.getContent();
                if (content.indexOf(call_block) < 0 && content.indexOf("{gallery}") < 0) {
                    ed.focus();
                    ed.selection.setContent(call_block);
                } else
                    ed.windowManager.alert('Вы уже вставили галерею');
            }
        });

        ed.addButton("cms_gallery_2", {
            title : 'Галерея (тип 2)',
            onclick : function() {
                var call_block = "{gallery}";
                var content = ed.getContent();
                var count = content.indexOf(call_block);
                if (count < 0 && content.indexOf("{simple_gallery}") < 0) {
                    ed.focus();
                    ed.selection.setContent(call_block);
                } else {
                    ed.windowManager.alert("Вы уже вставили галерею");
                }
            }
        });

        ed.addButton("cms_comments", {
            title : 'Комментарии',
            onclick : function() {
                var call_block = "{comments}";
                var content = ed.getContent();
                if (content.indexOf(call_block) > 0) {
                    ed.windowManager.alert("Вы уже вставили комментарии");
                } else {
                    ed.focus();
                    ed.selection.setContent(call_block);
                }
            }
        });
    }
});

tinymce.create('tinymce.plugins.GisMap', {
    init : function(ed, url) {
        var dialog_url = '<?php echo $gismapDialog; ?>';

        ed.addCommand('mceGisMap', function() {
            ed.windowManager.open({
                file : dialog_url,
                width : 700,
                height : 500,
                inline : 1
            }, {
                //plugin_url : url, // Plugin absolute URL
                //some_custom_arg : 'custom arg' // Custom argument
            });
        });

        ed.addButton('gismap', {
            title : 'Карта 2Гис',
            cmd : 'mceGisMap'
        });
    }
});

tinymce.PluginManager.add('gismap', tinymce.plugins.GisMap);
