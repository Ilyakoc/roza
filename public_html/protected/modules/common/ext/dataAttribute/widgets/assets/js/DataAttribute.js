/**
 * Data Attribute widget class
 */
var DataAttribute = {
	_data: [],
	
    /**
     * Initialization.
     * @param object options опции инициализации
     * attribute: (string) имя аттрибута
     * area: (string) селектор области, в которой находится содержимое виджета
     * по умолчанию ".daw-wrapper[data-item='<attribute>']"
     * enabeleSortable: (boolean) Инициализировать сортировку. По умолчанию TRUE.
     * sortableOptions: (object) опции сортировки плагина JQuery sortable. По умолчанию {cursor: "move"}.
     */
    init: function(options) {
        if(typeof(options.area) == 'undefined')
            options.area = ".daw-wrapper[data-item='" + options.attribute + "']";

        this._data[options.attribute] = {
            index: $(options.area).find(".daw-table tbody tr").length,
            area: $(options.area),
        }

        DataAttribute.bind($(options.area));

        if(options.enableSortable) {
            var sortableOptions=(typeof(options.sortableOptions) == "undefined") ? {cursor: "move"} : options.sortableOptions;
            $(options.area).find(".daw-table tbody").sortable(sortableOptions);
        }
    },
	
	/**
	 * Bind action events.
	 * @param jQuery $parent родетельский элемент, относительно которого 
	 * происходит поиск элементов. 
	 */
	bind: function($parent) {
		$parent.find(".daw-btn-remove").on("click", function(e) { e.preventDefault(); DataAttribute.remove(e) });
		$parent.find(".daw-btn-add").on("click", function(e) { e.preventDefault(); DataAttribute.add(e) });
		$parent.children("td[data-ajax-tpl-url]").each(function() {
			var $item=$(this);
			$.post($item.data("ajax-tpl-url"), {}, function(data) {
				$item.html(data);
			});
		});
	},
	
	/**
	 * Добавление нового элемента
	 * @param event e 
	 */
	add: function(e) {
		var data = DataAttribute._data[$(e.target).data('attribute')];
		var template = data.area.find(".daw-template tbody").html();
		var $tbody = data.area.find(".daw-table tbody");
		
		$tbody.append(template.replace(/{{daw-index}}/g, data.index));
		var $tr = $tbody.find("tr:last");
		$tr.find("input,select,textarea").attr("disabled", false);
		data.index++;
		
		DataAttribute.bind($tr);
		
		if($tbody.filter(":hidden").size()) {
			$tbody.siblings("thead").show();
			$tbody.show();
		}
	},
	
	/**
	 * Удаление
	 */
	remove: function(e) {
		var $tr = $(e.target).parents("tr:first");
		var $table = $tr.parents("table:first"); 
		
		if($tr.prop("rowIndex") < 0) return;
		
		$tr.remove();
		
		if($table.find("tbody tr").size() < 1) {
			$table.find("thead, tbody").hide();
		}
	}	
}