/**
 * Скрипты для виджета \common\ext\ymap\widgets\YMap 
 */

/**
 * Класс Сommon_Ext_YMap_Widgets_YMap
 * @param object options параметры инициализации
 * id - идентификатор контейнера карты (обязательный).
 * x - координата X центра (обязательный).
 * y - координата Y центра (обязательный).
 * geocode - адрес. Если передан, координаты "x" и "y" можно не передавать.
 * zoom - значение параметра увеличения. По умолчанию 17.
 * hint - подсказка балуна. По умчоланию не задана.
 * content - содержимое балуна. По умолчанию не задано. 
 * iconImageHref - ссылка на картинку балуна.
 * iconImageSize - размеры картинки балуна.
 */
var Сommon_Ext_YMap_Widgets_YMap = function(options) {
	var _this=this;
	
	var o=function(name, def, _options) {
		if(typeof(_options) == 'undefined') _options=options;
		if((typeof(_options) == "object") && (typeof(_options[name]) != "undefined")) {
			return _options[name];
		}
		return (typeof(def) == 'undefined') ? false : def;
	};
	
	if(!(o("geocode") || (o("x") && o("y")) || o("points"))) {
		return false;
	}
	
	var map;
	
	function init() {
		
		function add(x,y) {
			options["x"]=x;
			options["y"]=y;
			map = new ymaps.Map(o("id"), {
				center: [x, y],
				zoom: o("zoom"),
				scrollZoom: false,
				controls: ["zoomControl"]
			});
			_this.add(options, map);
		}

		if(o("geocode")) {
			$.get("https://geocode-maps.yandex.ru/1.x/?format=json", {geocode: o("geocode")}, function(response){
				if(response.response.GeoObjectCollection.featureMember.length) {
					var coords=response.response.GeoObjectCollection.featureMember[0].GeoObject.Point.pos.split(" ");
					add(coords[1], coords[0]);
				}
			},'json');
		}
		else {
			if(o("points")) {
				map = new ymaps.Map(o("id"), {center: [55.029030, 82.926474], zoom: o("zoom"), scrollZoom: false, controls: ["zoomControl"]});
				var mapCollection = new ymaps.GeoObjectCollection(); 
	            
				var placemarkOptions={};
	            if(o("iconImageHref")) {
	            	placemarkOptions={
            			iconLayout: 'default#image',
            			iconImageHref: o("iconImageHref"),
            			iconImageSize: o("iconImageSize", [40, 40])
	            	};
	            }
	            o("points").forEach(function(coords) {
	        		var placemark = new ymaps.Placemark([parseFloat(coords[1])+0.0001, parseFloat(coords[0])-0.00005], { 
	        			hintContent: o("hint", "", options), 
	        			balloonContent: o("content", "", options) 
	        		}, placemarkOptions);
	        		mapCollection.add(placemark);
				});
	            
	            map.geoObjects.add(mapCollection);
	            map.setBounds(mapCollection.getBounds(), {checkZoomRange:true, zoomMargin:10});
			}
			else {
				add(o("x"), o("y"));
			}
		}
	}
	
	ymaps.ready(init);
	
	/**
	 * Добавление балуна
	 * @param object options параметры для балуна
	 * x - координата X (обязательный).
	 * y - координата Y (обязательный).
	 * hint - подсказка балуна. По умчоланию не задана.
	 * content - содержимое балуна. По умолчанию не задано. 
	 */
	_this.add = function(options, map) {
		var x=o("x", false, options),
			y=o("y", false, options);
		
		if(!x || !y) return false;
		
		var placemarkOptions={};
		if(o("iconImageHref")) {
			placemarkOptions={
				iconLayout: 'default#image',
		        iconImageHref: o("iconImageHref"),
		        iconImageSize: o("iconImageSize", [40, 40])
			};
		}
		var placemark = new ymaps.Placemark([parseFloat(x)+0.0001, parseFloat(y)-0.00005], { 
			hintContent: o("hint", "", options), 
			balloonContent: o("content", "", options) 
		}, placemarkOptions);
		
		map.geoObjects.add(placemark);
	};
	
	return _this;
};