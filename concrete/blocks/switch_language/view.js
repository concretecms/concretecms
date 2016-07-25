$(function() {

	$('select[data-select=multilingual-switch-language]').change(function() {
        var action = $(this).attr('data-action').replace('--language--', $(this).val());
        window.location.href = action;
	});
});