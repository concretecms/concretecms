
ccm_layoutRefresh = function() {
	var columns = parseInt($('#ccm-layouts-toolbar select[name=columns]').val());
	if (columns < 2) {
		$('#ccm-layouts-toolbar input[name=spacing]').prop('disabled', true);
		$('#ccm-layouts-toolbar input[name=isautomated]').prop('disabled', true);
	} else {
		$('#ccm-layouts-toolbar input[name=spacing]').prop('disabled', false);
		$('#ccm-layouts-toolbar input[name=isautomated]').prop('disabled', false);
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

	$("#ccm-area-layout-active-control-bar").slider('destroy');

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

ccm_themeGridSliderFindNearest = function(value, values) {

	var nearest = null;
	var diff = null;
	for (var i = 0; i < values.length; i++) {
		if ((values[i] <= value) || (values[i] >= value)) {
			var newDiff = Math.abs(value - values[i]);
			if (diff == null || newDiff < diff) {
				nearest = values[i];
				diff = newDiff;
			}
		}
	}
	return nearest;
}

ccm_themeGridRefresh = function() {
	var columns = parseInt($('#ccm-layouts-toolbar select[name=themeGridColumns]').val());

	if (!($('.ccm-theme-grid-column-edit-mode').length)) {
		$('#ccm-layouts-edit-mode').html('');
		var $form = $('#ccm-layouts-edit-mode');
		var row = ccm_themeGridSettings.rowStartHTML;
		row += '<div id="ccm-theme-grid-edit-mode-row-wrapper">';
		var columnSpans = ccm_themeGridGetColumnSpans(columns);
		$.each(columnSpans, function(i, spanInfo) {

			// get the class at the starting rowspan
			var columnHTML = '<div id="ccm-edit-layout-column-' + i + '" class="' + spanInfo.cssClass + ' ccm-theme-grid-column" data-offset="0" data-span="' + spanInfo.value + '"><div class="ccm-layout-column-highlight"><input type="hidden" id="ccm-edit-layout-column-offset-' + i + '" name="offset[' + i + ']" value="0" /><input type="hidden" id="ccm-edit-layout-column-span-' + i + '" name="span[' + i + ']" value="' + spanInfo.value + '" /></div></div>';
			// now, sometimes we might need to set the next column to a smaller amount
			row += columnHTML;

		});

		row += '</div>';
		row += ccm_themeGridSettings.rowEndHTML;
		$form.append(row);

	}

	$("#ccm-area-layout-active-control-bar").slider('destroy');

	if (columns > 1) {
		// set the breakpoints

		var breaks = [];
		for (i = 0; i < columns; i++) {

			$column = $('#ccm-edit-layout-column-' + i);

			if (i == 0) {
				// this is the first column so we only get the end
				breaks.push(parseInt($column.width()));
			} else if ((i + 1) == columns) {
				breaks.push(parseInt($column.position().left));
			} else {
				breaks.push(parseInt($column.position().left));
				breaks.push(parseInt($column.width() + $column.position().left));
			}

		}

		// set the valid widths
		var tw = $('#ccm-area-layout-active-control-bar').width();
		var sw = 0;
		var validStartPoints = [];
		var validEndPoints = [];

		var maxColumns = ccm_themeGridSettings.maxColumns;
		var minColumnClass = ccm_themeGridSettings.columnClasses[0];

		$('<div />', {'id': 'ccm-theme-grid-temp'}).appendTo(document.body);
		var columnHTML = '';
		for (i = 1; i <= maxColumns; i++) {
			columnHTML += '<div class="' + minColumnClass + '"></div>'
		}
		$('#ccm-theme-grid-temp').append($(ccm_themeGridSettings.rowStartHTML + columnHTML + ccm_themeGridSettings.rowEndHTML));
		var marginModifier = 0;
		for (i = 0; i < maxColumns; i++) {
			var $column = $($('#ccm-theme-grid-temp .' + minColumnClass).get(i));
			if (i == 0) {
				var pl = $column.position().left;
				if (pl < 0) {
					marginModifier = Math.abs(pl); // handle stupid grids that have negative margin starters
				}
			}
			// handle the START of every column
			validStartPoints.push(parseInt($column.position().left + marginModifier));

			// handle the END of every column
			validEndPoints.push(parseInt($column.width() + $column.position().left + marginModifier));

		}

		$('#ccm-theme-grid-temp').remove();

		var themeGridSlider = $("#ccm-area-layout-active-control-bar").css('height', '12px').slider({
			min: 0,
			max: tw,
			step: 1,
			values: breaks,

			slide: function (e, ui) {
				var index = $(ui.handle).index();
				var pointsToCheck;

				if ((index % 2) == 0) {
					pointsToCheck = validEndPoints;
				} else {
					pointsToCheck = validStartPoints;
				}

				var oldValue = themeGridSlider.slider('values', index);
				var newValue = ccm_themeGridSliderFindNearest(ui.value, pointsToCheck);
				// now we determine whether we CAN go there or is it going to encroach upon another point.
				var proceed = true;
				$.each(ui.values, function(i, value) {
					if (newValue >= value && index < i) {
						proceed = false;
					} else if (newValue <= value && index > i) {
						proceed = false;
					}
				});

				if (proceed) {
					themeGridSlider.slider('values', index, newValue);
					if (oldValue != newValue) {
						if ((index % 2) == 0) {
							var i = Math.floor(index / 2);
							// we are a righthand handle
							$innercolumn = $('#ccm-edit-layout-column-' + i);
							var span = parseInt($innercolumn.attr('data-span'));
							var $offsetcolumn = $innercolumn.nextAll('.ccm-theme-grid-column:first');
							var offset = $offsetcolumn.attr('data-offset');
							if (offset) {
								offset = parseInt(offset);
							} else {
								offset = 0;
							}
							if (newValue > oldValue) { // we are making the column bigger
								span++;
								offset--;
							} else {
								span--;
								offset++;
							}
						} else {
							// we are a righthand handle
							var i = Math.ceil(index / 2);
							$innercolumn = $('#ccm-edit-layout-column-' + i);
							var span = parseInt($innercolumn.attr('data-span'));
							var $offsetcolumn = $innercolumn;
							var offset = $offsetcolumn.attr('data-offset');
							if (offset) {
								offset = parseInt(offset);
							} else {
								offset = 0;
							}
							if (newValue < oldValue) { // we are making the column bigger
								span++;
								offset--;
							} else {
								span--;
								offset++;
							}
						}
						$offsetcolumn.attr('data-offset', offset);
						$innercolumn.attr('data-span', span);
						ccm_themeGridRefreshDimensions();
					}
				}
				return false;

			}

		});
	} else {
		if ($("#ccm-area-layout-active-control-bar").hasClass('ccm-area-layout-control-bar-add')) {
			$('#ccm-area-layout-active-control-bar').css('height', '0px');
		}
	}
}

ccm_themeGridRefreshDimensions = function() {
	var $columns = $('#ccm-layouts-edit-mode .ccm-theme-grid-column');
	var $offsets = $('#ccm-layouts-edit-mode .ccm-theme-grid-offset-column');
	$offsets.remove();
	$.each($columns, function(i, col) {
		var $col = $(col);
		var isedit = $(col).hasClass('ccm-theme-grid-column-edit-mode');
		$col.removeClass().addClass('ccm-theme-grid-column');
		if (isedit) {
			$col.addClass('ccm-theme-grid-column-edit-mode');
		}
		if ($col.attr('data-span')) {
			var spandex = parseInt($col.attr('data-span')) - 1;
			$col.addClass(ccm_themeGridSettings.columnClasses[spandex]);
			// change the span value inside
			$('#ccm-edit-layout-column-span-' + i).val(parseInt($col.attr('data-span')));
		}
		if ($col.attr('data-offset')) {
			var offdex = parseInt($col.attr('data-offset')) - 1;
			$('<div />', {'data-offset-column': true}).addClass('ccm-theme-grid-offset-column').addClass(ccm_themeGridSettings.columnClasses[offdex]).insertBefore($col);
			$('#ccm-edit-layout-column-offset-' + i).val(parseInt($col.attr('data-offset')));
		}

	});
}

ccm_initLayouts = function() {

	// theme grid chooser
	$('#ccm-layouts-toolbar select[name=useThemeGrid]').change(function() {
		ccm_gridTypeRefresh();
		ccm_themeGridRefresh();
	});

	// theme grid layout controls
	$('#ccm-layouts-toolbar select[name=themeGridColumns]').change(function() {
		ccm_themeGridRefresh();
	});

	// free-form layout controls
	$('#ccm-layouts-toolbar select[name=columns]').change(function() {
		$('#ccm-layouts-edit-mode').html('');
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