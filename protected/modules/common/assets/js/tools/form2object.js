/**
 * Преобразование элементов формы в объект.
 * 
 * @param array elements массив элементов формы. 
 * Может быть передан объект jQuery, напр., 
 * результат $(form).find("select,textarea,input:not(:submit):not(:button)")
 *
 * Напр., для использования с \CListView
 * var $elements=$(this).find("select,textarea,input:not(:submit):not(:button)"),
 * data=kontur_form2object($elements);
 * if($.fn.yiiListView) $.fn.yiiListView.update('ajaxListView', {data: data});
 */
function kontur_form2object(elements) 
{
    var data={};
	$(elements).each(function(idx, elm) {
		var current=data;			
		$(elm).attr("name").split("[").forEach(function(name,idx,arr) {
			name=name.replace(/\]+/, '');
			if(idx==(arr.length-1)) {
                var i=0; for(var c in current) i++;
                current[name]=$(elm).val();
            }
            else {
                var founded=false;
                for(var c in current) if(c == name) founded=true;
                if(!founded) current[name]={};
                current=current[name];
            }
        });
	});
	return data;
}