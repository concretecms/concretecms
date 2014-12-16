$(function() {

	$('select[data-select=multilingual-switch-language]').change(function() {
		$(this).parent().submit();
	});
});