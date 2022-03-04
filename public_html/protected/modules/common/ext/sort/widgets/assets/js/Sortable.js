/**
 * Javascript for \common\ext\sort\widgets\Sortable widget.
 */

/**
 * Класс сортировки для плагина jQuery UI Sortable. 
 * @param object options параметры.
 * Параметры:
 * "category" (string) имя категории сортировки.
 * "key" (int|null) ключ категории сортировки.
 * "level" (int) уровень. может использоваться при постраничной сортировке.
 * "selector" (string) выражение выборки (jQuery) родительского элемента.
 * "saveUrl" (string) ссылка на действие сохранения
 * "dataId" (string) имя атрибута сортировки, в котором будут хранится id модели.
 * По умолчанию "data-sort-id".
 * "autosave" (boolean) автоматически сохранять сортировку. 
 * По умолчанию (TRUE) - сохранять.
 * "onAfterSave" (callable) обработчик после сохранения 
 * function(PlainObject data, String textStatus, jqXHR jqXHR) 
 */
;var CommonExtSortWidgetSortable=function(options) {
	var _this=this;
	
	/**
	 * @var boolean плагин инициализирован.
	 */
	_this.initialized=false;
	
	/**
	 * Получить значение параметра
	 * @param string name имя параметра.
	 * @param mixed def значение параметра по умолчанию.
	 * По умолчанию NULL.
	 */
	function o(name, def) {
		if(typeof(options[name]) == 'undefined') {
			if(typeof(def) == 'undefined') return null;
			return def;
		}
		return options[name];
	}
	
	/**
	 * Инициализация.
	 * @return object|NULL результат возвращаемый jQuery.sortable()
	 */
	_this.init=function() {
		if(o("selector")) {
			_this.initialized=true;
			return $(o("selector")).sortable({
				cursor: "move",
			    stop: function(event, ui) {
			    	if(o("autosave", true)) _this.save();
			    }
			});
		}
		return null;
	}
	
	/**
	 * Сохранение сортировки.
	 */
	_this.save=function() {
		if(_this.initialized) {
			var data = $(o("selector")).sortable("toArray", {attribute: o("dataId", "data-sort-id")});
	        $.post(o("saveUrl"), {category: o("category"), key: o("key"), data: data, level: o("level", 0)}, o("onAfterSave", null));
		}
	}
	
	return _this;
};
