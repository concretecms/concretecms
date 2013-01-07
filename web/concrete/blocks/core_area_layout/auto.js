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
	var columnwidths = [];
	for (i = 0; i < columns; i++) {
		$highlight = $('#ccm-edit-layout-column-' + i + ' .ccm-layout-column-highlight');
		if (i > 0) {
			$highlight.css('margin-left', (spacing / 2) + 'px');
		}
		if ((i + 1) < columns) {
			$highlight.css('margin-right', (spacing / 2) + 'px');
		}
		$column = $('#ccm-edit-layout-column-' + i);
		if ($column.attr('data-width')) {
			var width = $column.attr('data-width') + 'px';
			columnwidths.push(parseInt($column.attr('data-width')));
		} else {
			var width = (100 / columns) + '%';
		}
		$column.css('width', width);		
	}


	if (columns > 1 && (!$('#ccm-layouts-toolbar input[name=isautomated]').is(':checked'))) {
		var breaks = [];
		var sw = 0;
		var tw = $('#ccm-area-layout-active-control-bar').width();
		if (columnwidths.length > 0) {
			// we have custom column widths
			for (i = 0; i < (columnwidths.length - 1); i++) {
				sw += columnwidths[i];
				breaks.push(sw);
			}
		} else {
			var cw = tw / columns;
			for (i = 1; i < columns; i++) {
				sw += cw;
				breaks.push(sw);
			}
		}

		var $columns = $("#ccm-area-layout-active-control-bar").parent().find('#ccm-layouts-edit-mode .ccm-layout-column');
		$("#ccm-area-layout-active-control-bar").css('height', '12px').slider({
			min: 0,
			max: tw,
			step: 1,
			values: breaks,
			create: function(e, ui) {
				var createoffset = 0;
				var breakwidths = [];

				$.each($columns, function(i, col) {
					var bw = breaks[i];
					if ((i + 1) == $columns.length) {
						// last column
						var value = tw - createoffset;
					} else {
						var value = bw - createoffset;
					}
					var value = Math.floor(value);
					$(col).find('#ccm-edit-layout-column-width-' + i).val(value);
					createoffset = bw;
				});

			},
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
						var value = Math.floor(value);
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
		if ($("#ccm-area-layout-active-control-bar").hasClass('ccm-area-layout-control-bar-add')) {
			$('#ccm-area-layout-active-control-bar').css('height', '0px');
		}

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