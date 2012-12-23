ccm_layoutRefresh = function() {
	var columns = parseInt($('#ccm-layouts-toolbar select[name=columns]').val());
	if (columns < 2) {
		$('#ccm-layouts-toolbar input[name=spacing]').prop('disabled', true);
	} else {
		$('#ccm-layouts-toolbar input[name=spacing]').prop('disabled', false);
	}
	var $form = $('#ccm-layouts-edit-mode');
	var spacing = $('#ccm-layouts-toolbar input[name=spacing]').val();
	for (i = 0; i < columns; i++) {
		if ($('#ccm-edit-layout-column-' + i).length > 0) {
			continue;
		}

		var $column = $('<div />').attr('class', 'ccm-layout-column');
		$column.attr('id', 'ccm-edit-layout-column-' + i);
		var $highlight = $('<div />').attr('class', 'ccm-layout-column-highlight');
		$highlight.append($('<input />', {'name': 'width[' + i + ']', 'type': 'hidden', 'id': 'ccm-edit-layout-column-width-'+ i}));
		$column.append($highlight);
		$form.append($column);
	}

	// now we remove unused columns
	var $realcolumns = $('#ccm-layouts-edit-mode .ccm-layout-column');
	if (columns < $realcolumns.length) {
		for (i = columns; i < $realcolumns.length; i++ ){
			$('#ccm-edit-layout-column-' + i).remove();
		}
	}
	
	// now we handle spacing
	for (i = 0; i < columns; i++) {
		$highlight = $('#ccm-edit-layout-column-' + i + ' .ccm-layout-column-highlight');
		if (i > 0) {
			$highlight.css('margin-left', (spacing / 2) + 'px');
		}
		if ((i + 1) < columns) {
			$highlight.css('margin-right', (spacing / 2) + 'px');
		}
		$column = $('#ccm-edit-layout-column-' + i);
		var width = (100 / columns) + '%';
		$column.css('width', width);		
	}


	if (columns > 1 && (!$('#ccm-layouts-toolbar input[name=isautomated]').is(':checked'))) {
		var breaks = [];
		var tw = $('#ccm-area-layout-active-control-bar').width();
		var cw = tw / columns;
		var sw = 0;

		for (i = 1; i < columns; i++) {
			sw += cw;
			breaks.push(sw);
		}

		var $columns = $("#ccm-area-layout-active-control-bar").parent().find('#ccm-layouts-edit-mode .ccm-layout-column');
		$("#ccm-area-layout-active-control-bar").slider({
			min: 0,
			max: tw,
			step: 1,
			values: breaks,
			slide: function (e, ui) {
				var lastvalue = 0,
					proceed = true;

				$.each(ui.values, function(i, value) {
					if (value < lastvalue) {
						proceed = false;
					}
					lastvalue = value;
				});

				if (proceed) {
					lastvalue = 0;
					$.each($columns, function(i, col) {

						if ((i + 1) == $columns.length) {
							// last column
							var value = tw - lastvalue;
						} else {
							var value = ui.values[i] - lastvalue;
						}
						$(col).find('#ccm-edit-layout-column-width-' + i).val(value);
						$(col).css('width', value + 'px');
						lastvalue = ui.values[i];
					});
				} else {
					return false;
				}
			}
		});
	} else {
		$("#ccm-area-layout-active-control-bar").slider('destroy');
	}

}

$(function() {

	$('#ccm-layouts-toolbar select[name=columns]').change(function() {
		ccm_layoutRefresh();
	});

	$('#ccm-layouts-toolbar input[name=spacing]').change(function() {
		ccm_layoutRefresh();
	});

	$('#ccm-layouts-toolbar input[name=isautomated]').on('click', function() {
		ccm_layoutRefresh();
	});

	ccm_layoutRefresh();

	$('#ccm-layouts-cancel-button').on('click', function() {
		ccm_onInlineEditCancel();
	});
	$('#ccm-layouts-save-button').on('click', function() {
		$('#ccm-block-form').submit();
	});


	$('#ccm-layouts-cancel-button').on('click', function() {
		ccm_onInlineEditCancel();
	});

});