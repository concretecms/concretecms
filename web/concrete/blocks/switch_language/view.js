$(function() {
	$('select[name=ccmMultilingualChooseLanguage]').change(function() {
		$(this).parent().submit();
	});
});