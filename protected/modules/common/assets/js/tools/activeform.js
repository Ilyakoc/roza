/**
 * Вспомогательный класс для \CActiveForm
 */
function kontur_activeform=(function() {
	var _this=this;
	
	/** @var mixed form объект или выражение выборки формы. */ 
	this.form=null;
	
	/** @var string exprRow выражение выборки обертки блока элемента формы. */
	this.exprRow='';
	
	/** @var string modelPrefix префикс имени модели атрибута "id" элемента формы. */
	this.modelPrefix='';
	
	/**
	 * Обработчик при наличии ошибок.
	 * @param mixed data данные формы. \CActiveForm[clientOptions[afterValidate]]
	 */
	this.hasError=function(data) {
		$(_this.form).find(_this.exprRow).removeClass("error");
    	for(var key in data) {
    		if(key.indexOf(_this.modelPrefix) === 0) {
    			$(_this.form).find("#"+key).parents(_this.exprRow+":first").addClass("error");
    		}
    	}
	};
	
	/**
	 * Отображение ошибок после ajax запроса.
	 * @param mixed errors массив ошибок, возвращаемый CFormModel::validate(). 
	 */
	this.errorResponce=function(errors) {
		$(_this.form).find(_this.exprRow+".error").removeClass("error");
		$(_this.form).find(_this.exprRow+" .errorMessage").hide();
		for(var key in errors) {
			var $field=$(_this.form).find("#" + _this.modelPrefix + key);
			if($field.length) {
				$field.parents(_this.exprRow + ":first").removeClass("success").addClass("error");
				var $error=$(_this.form).find("#" + _this.modelPrefix + key + "_em_");
				$error.html(errors[key]);
				$error.show();
			}
		}
	};
	
	return function(form, exprRow, modelPrefix) {
		_this.form=form;
		_this.exprRow=exprRow;
		_this.modelPrefix=modelPrefix;
		
		return _this;
	};
})();
