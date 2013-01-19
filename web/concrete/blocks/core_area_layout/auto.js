
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

ccm_gridTypeRefresh = function() {
	var $ust = $('#ccm-layouts-toolbar select[name=useThemeGrid]');
	if ($ust.length) {
		if ($ust.val() == 1) {
			$('#ccm-layouts-toolbar li[data-grid-control=layout]').hide();
			$('#ccm-layouts-toolbar li[data-grid-control=page-theme]').show();
		} else {
			$('#ccm-layouts-toolbar li[data-grid-control=page-theme]').hide();
			$('#ccm-layouts-toolbar li[data-grid-control=layout]').show();
		}
	}
}

ccm_themeGridGetColumnSpans = function(totalColumns) {
	$('#ccm-layouts-edit-mode').html('');
	var maxColumnSize = parseInt($('#ccm-layouts-toolbar select[name=themeGridColumns] option:last-child').val());
	var rowSpan = Math.ceil(maxColumnSize / totalColumns);
	// create the starting array
	var spanArray = [];
	for (i = 0; i < totalColumns; i++) {
		spanArray[i] = rowSpan;
	}
	var rowSpanTotal = rowSpan * totalColumns;
	for (i = 0; i < (rowSpanTotal - maxColumnSize); i++) {
		var index = spanArray.length - i - 1;
		spanArray[index]--;
	}

	var columnClasses = [];
	for (i = 0; i < spanArray.length; i++) {
		columnClasses[i] = {};
		columnClasses[i].cssClass = ccm_themeGridSettings.columnClasses[spanArray[i]-1];
		columnClasses[i].value = spanArray[i];
	}

	return columnClasses;
}

ccm_themeGridRefresh = function() {
	var columns = parseInt($('#ccm-layouts-toolbar select[name=themeGridColumns]').val());
	var $form = $('#ccm-layouts-edit-mode');
	var row = ccm_themeGridSettings.rowStartHTML;
	var columnSpans = ccm_themeGridGetColumnSpans(columns);
	$.each(columnSpans, function(i, spanInfo) {

		// get the class at the starting rowspan
		var columnHTML = '<div class="' + spanInfo.cssClass + ' ccm-theme-grid-column"><div class="ccm-layout-column-highlight"><input type="hidden" id="ccm-edit-layout-column-span-' + i + '" name="span[' + i + ']" value="' + spanInfo.value + '" /></div></div>';
		// now, sometimes we might need to set the next column to a smaller amount
		row += columnHTML;

	});

	row += ccm_themeGridSettings.rowEndHTML;
	$form.append(row);

	//	$highlight.append($('<input />', {'name': 'width[' + i + ']', 'type': 'hidden', 'id': 'ccm-edit-layout-column-width-'+ i}));
	//		$column.append($highlight);
}


ccm_initLayouts = function() {

	// theme grid chooser
	$('#ccm-layouts-toolbar select[name=useThemeGrid]').change(function() {
		ccm_gridTypeRefresh();
	});

	// theme grid layout controls
	$('#ccm-layouts-toolbar select[name=themeGridColumns]').change(function() {
		ccm_themeGridRefresh();
	});

	// free-form layout controls
	$('#ccm-layouts-toolbar select[name=columns]').change(function() {
		ccm_layoutRefresh();
	});

	$('#ccm-layouts-toolbar input[name=spacing]').change(function() {
		ccm_layoutRefresh();
	});

	$('#ccm-layouts-toolbar input[name=isautomated]').on('click', function() {
		ccm_layoutRefresh();
	});

	ccm_gridTypeRefresh();
	if ($('#ccm-layouts-toolbar select[name=useThemeGrid]').val() == 1) {
		ccm_themeGridRefresh();
	} else {
		ccm_layoutRefresh();
	}

	$('#ccm-layouts-cancel-button').on('click', function() {
		ccm_onInlineEditCancel();
	});
	$('#ccm-layouts-save-button').on('click', function() {
		$('#ccm-block-form').submit();
	});


	$('#ccm-layouts-cancel-button').on('click', function() {
		ccm_onInlineEditCancel();
	});

}