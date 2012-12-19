ccm_layoutRefresh = function() {
	var columns = $('#ccm-layouts-toolbar select[name=columns]').val();
	if (columns < 2) {
		$('#ccm-layouts-toolbar input[name=spacing]').prop('disabled', true);
	} else {
		$('#ccm-layouts-toolbar input[name=spacing]').prop('disabled', false);
	}
	var $form = $('#ccm-layouts-edit-mode');
	var spacing = $('#ccm-layouts-toolbar input[name=spacing]').val();
	$form.html('');
	for (i = 0; i < columns; i++) {
		var $column = $('<div />').attr('class', 'ccm-layout-column');
		var width = (100 / columns) + '%';
		$column.css('width', width);		
		var $highlight = $('<div />').attr('class', 'ccm-layout-column-highlight');
		if (i > 0) {
			$highlight.css('margin-left', (spacing / 2) + 'px');
		}
		if ((i + 1) < columns) {
			$highlight.css('margin-right', (spacing / 2) + 'px');
		}
		$column.append($highlight);
		$form.append($column);
	}
}

$(function() {

	$('#ccm-layouts-toolbar select[name=columns]').change(function() {
		ccm_layoutRefresh();
	});

	$('#ccm-layouts-toolbar input[name=spacing]').change(function() {
		ccm_layoutRefresh();
	});

	ccm_layoutRefresh();

	$('#ccm-layouts-cancel-button').on('click', function() {
		ccm_onInlineEditCancel();
	});
	$('#ccm-layouts-save-button').on('click', function() {
		$('#ccm-block-form').submit();
	});

});