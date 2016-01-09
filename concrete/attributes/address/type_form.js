$(function() {
	$("input[name=akHasCustomCountries]").click(function() {
		ccm_attributeTypeAddressCountries($(this));
	});
	
	ccm_attributeTypeAddressCountries();
});

ccm_attributeTypeAddressCountries = function(obj) {
	if (!obj) {
		var obj = $("input[name=akHasCustomCountries][checked=checked]");
	}
	if (obj.attr('value') == 1) {
		$("#akCustomCountries").attr('disabled' , false);
	} else {
		$("#akCustomCountries").attr('disabled' , true);
		$("#akCustomCountries option").attr('selected', true);
	}
}
