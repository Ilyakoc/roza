/**
 * Html helper
 *
 * @version 1.01
 *
 * Using:
 * kontur_html.adjustRow("#ul li", 3)
 *
 * kontur_html.adjustRow("#ul li", 3, 300)
 *
 * kontur_html.adjustRow("#ul li", 3, 300, function() { kontur_html.bottom("#ul li",".bottom-block",-30); });
 *
 * Выравнивание внутренных элементов списка.
 kontur_html.adjustRow("#ul li", 3, 300, function(parent) {
 	var selectors = [parent, ".child"];
 	kontur_html.setHeight(selectors, kontur_html.maxHeight(selectors));
 }); 
 *
 * @history
 * 1.01
 *  - параметр selector метода maxHeight() теперь может принимать вложенный поиск.
 *  - добавлен метод setHeight()
 */

var kontur_html = {
	/**
	 * Получить максимальную высоту
	 * @param mixed selector селектор. Может быть также передан массив [parent,child].
	 */
	maxHeight: function(selector) {
		var maxHeight = 0;
		var isPair = (typeof(selector) == 'object') && !(selector instanceof jQuery);
		var parentSelector = isPair ? selector[0] : selector;
		var $child;

        $(parentSelector).each(function() { 
        	$child = isPair ? $(this).find(selector[1]) : $(this);
            if($child.height() > maxHeight) 
            	maxHeight = $child.height();
        });
        
        return maxHeight;
	},

	/**
	 * Устанавливает высоту элементам.
	 * @param mixed selector селектор. Может быть также передан массив [parent,child].
	 * @param integer height высота.
	 */
	setHeight: function(selector, height) {
		if(typeof(selector) != 'object') $(selector).height(height);
		else {
        	$(selector[0]).each(function() { 
        		$(this).find(selector[1]).height(height);
	        });
	    }
	},
	
	/**
	 * Выравнивание элементов в строке
	 * 
	 * @param itemSelector селектор элементов 
	 * @param countPerRow количество элементов в строке. 
	 * По умолчанию (=0,undefined) все элементы в одной строке.
	 * @param maxIntervalCount количество повторений операции выравнивания.
	 * По умолчанию (=0,undefined) не повторять.
	 * 
	 * @returns
	 */
	adjustRow: function(itemSelector, countPerRow, maxIntervalCount, hAfter) {
		if(isNaN(+countPerRow)) countPerRow = 0;
		
		var $itemSelector = $(itemSelector);
		
	    $itemSelector.css("height", "");
	    
		if(countPerRow > 0) {
			var i, $items;
			for(i=0; i<$itemSelector.length; i+=countPerRow) {
				$items = $itemSelector.slice(i, i+countPerRow);
				$items.height(HHtml.maxHeight($items));
				if(typeof(hAfter) == 'function') hAfter($items);
			}
		}   
		else {
			$itemSelector.height(HHtml.maxHeight($itemSelector));
			if(typeof(hAfter) == 'function') hAfter();
		}
		
		
		
		if(!isNaN(+maxIntervalCount) && (maxIntervalCount > 0)) {
			var _aprIntervalCount = 0;
			var _aprIntervalId = setInterval(function () {
				_aprIntervalCount++;
				if(HHtml._downStep(_aprIntervalCount)) return false;
				
				HHtml.adjustRow(itemSelector, countPerRow, 0, hAfter);
				if(_aprIntervalCount > +maxIntervalCount) clearInterval(_aprIntervalId);
			}, 200);
		}
	},
	
	/**
	 * Прижатие к низу элемента (childSelector) относительно (parentSelector)
	 * @param parentSelector
	 * @param childSelector
	 * @param topOffset
	 * @param leftOffset
	 * @returns
	 */
	bottom: function(parentSelector, childSelector, topOffset, leftOffset) {
		$(parentSelector).each(function() { 
			$(this).find(childSelector).offset({
				top: $(this).height() + $(this).offset().top + (isNaN(+topOffset) ? 0 : +topOffset)
			});
			
			if(!isNaN(+leftOffset)) {
				$(this).find(childSelector).offset({
					left: $(this).offset().left + (isNaN(+leftOffset) ? 0 : +leftOffset)
				});
			}
		});
	},
	
	_downStep: function(step, stepStart, stepPer) {
		if(isNaN(stepStart)) stepStart = 20;
		if(isNaN(stepPer)) stepPer = 5;
	
		if(step > 2*stepStart) return HHtml._downStep(step, 2*stepStart, 2*stepPer);
		
		return (step % stepPer) ? true : false;
	}
}
