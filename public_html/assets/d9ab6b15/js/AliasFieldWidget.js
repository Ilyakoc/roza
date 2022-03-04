/**
 * AliasFieldWidget script
 */
function AliasFieldWidget(titleId, aliasId, isNewRecord) {
	var afw = {
		titleId: titleId,
		aliasId: aliasId,
		isNewRecord: isNewRecord,
		
		init: function() {
			if(afw.isNewRecord) {
				$('#'+afw.titleId).on('keyup', afw.update);
			}
			else {
				$('#'+afw.aliasId).siblings('.js-afw-btn-update').on('click', afw.update);
			}
			return this;
		},
		
		update: function() {
			//Если с английского на русский, то передаём вторым параметром true.
			$('#'+afw.aliasId).val(cyr2lat($('#'+afw.titleId).val()));
		}
	};
	
	afw.init();
	
	return afw;
}