/** 
 * Free-Form Layouts
 */

// plugin
jQuery.fn.ccmlayout = function(options) {
	return this.each(function()	{
		var $obj = $(this);
		var data = $obj.data('ccmlayout');
		if (!data) {
			$obj.data('ccmlayout', (data = new CCMLayout(this, options)));
		}
	});
};

// initialization
var CCMLayout = function(element, options) {
	this.options = $.extend({
		'toolbar': '#ccm-layouts-toolbar',
		'btnsave': '#ccm-layouts-save-button',
		'btncancel': '#ccm-layouts-cancel-button',
		'editing': false,
		'formview': 'custom',
		'supportsgrid': false,
		'gridrowtmpid': 'ccm-theme-grid-temp'
	}, options);

	this.$element = $(element);
	this.$toolbar = $(this.options.toolbar);
	this.$toolbar.prependTo(document.body);

	this._setupDOM();
	if (this.options.formview == 'choosetype') {
		this._setupToolbarView(true);
	} else {
		this._setupToolbarView();
	}
	this._setupFormSaveAndCancel();
	this._setupFormEvents();

	switch(this.options.formview) {
		case 'choosetype':
			this._updateChooseTypeForm();
			if (this.usethemegrid) {
				this._updateThemeGridView();
			} else {
				this._updateCustomView();
			}
			break;
		case 'themegrid':
			this._updateThemeGridView();
			break;
		default: // custom
			this._updateCustomView();
			break;
	}

}

// private methods
CCMLayout.prototype._setupDOM = function() {
	// form list items
	this.$formviews = this.$toolbar.find('li[data-grid-form-view]');
	this.$formviewcustom = this.$toolbar.find('li[data-grid-form-view=custom]');
	this.$formviewchoosetype = this.$toolbar.find('li[data-grid-form-view=choosetype]');
	this.$formviewthemegrid = this.$toolbar.find('li[data-grid-form-view=themegrid]');

	// choosetype option
	this.$usethemegrid = this.$toolbar.find('select[name=useThemeGrid]');

	// choosetype + custom
	this.$selectcolumnscustom = this.$toolbar.find('select[name=columns]');
	this.$customspacing = this.$toolbar.find('input[name=spacing]');
	this.$customautomated = this.$toolbar.find('input[name=isautomated]');

	// choosetype + themegrid
	this.$selectgridcolumns = this.$toolbar.find('select[name=themeGridColumns]');

	// all
	this.$savebtn = this.$toolbar.find(this.options.btnsave);
	this.$cancelbtn = this.$toolbar.find(this.options.btncancel);
	this.$slider = false;
}

CCMLayout.prototype._setupFormSaveAndCancel = function() {
	var obj = this;
	this.$cancelbtn.on('click', function() {
		ccm_onInlineEditCancel(function() {
			obj.$toolbar.remove();
		});
	});
	this.$savebtn.on('click', function() {
		// move the toolbar back into the form so it submits. so great.
		obj.$toolbar.hide().prependTo('#ccm-block-form');
		$('#ccm-block-form').submit();
	});
}

CCMLayout.prototype._setupToolbarView = function(hide) {
	var obj = this;
	this.$formviews.each(function(i) {
		if ($(this).attr('data-grid-form-view') != obj.options.formview) {
			if (hide) {
				$(this).hide();
			} else {
				$(this).remove();
			}
		}
	});
}

CCMLayout.prototype._updateChooseTypeForm = function() {
	this.usethemegrid = parseInt(this.$usethemegrid.val());
	if (this.options.formview == 'choosetype' && this.usethemegrid) {
		this.$formviewcustom.hide();
		this.$formviewthemegrid.show();
	} else {
		this.$formviewthemegrid.hide();
		this.$formviewcustom.show();
	}
}


CCMLayout.prototype._setupFormEvents = function() {
	var obj = this;
	this.$selectcolumnscustom.on('change', function() {
		obj._updateCustomView();
	});
	this.$customspacing.on('change', function() {
		obj._updateCustomView();
	});
	this.$customautomated.on('change', function() {
		obj._updateCustomView();
	});
	this.$selectgridcolumns.on('change', function() {
		obj._updateThemeGridView();
	});
	this.$usethemegrid.on('change', function() {
		obj._updateChooseTypeForm();
		if (obj.usethemegrid) {
			obj._updateThemeGridView();
		} else {
			obj._updateCustomView();
		}
	});
}

CCMLayout.prototype.buildThemeGridGrid = function() {
	this.$element.html('');

	var row = this.options.rowstart;
	row += '<div id="ccm-theme-grid-edit-mode-row-wrapper">';

	var columnSpans = this._getThemeGridColumnSpan(this.columns);
	$.each(columnSpans, function(i, spanInfo) {
		// get the class at the starting rowspan
		var columnHTML = '<div id="ccm-edit-layout-column-' + i + '" class="' + spanInfo.cssClass + ' ccm-theme-grid-column" data-offset="0" data-span="' + spanInfo.value + '"><div class="ccm-layout-column-highlight"><input type="hidden" id="ccm-edit-layout-column-offset-' + i + '" name="offset[' + i + ']" value="0" /><input type="hidden" id="ccm-edit-layout-column-span-' + i + '" name="span[' + i + ']" value="' + spanInfo.value + '" /></div></div>';
		// now, sometimes we might need to set the next column to a smaller amount
		row += columnHTML;
	});

	row += '</div>';
	row += this.options.rowend;
	this.$element.append(row);
}

CCMLayout.prototype._updateThemeGridView = function() {

	// load the current elements from forms
	this.columns = parseInt(this.$selectgridcolumns.val());
	this.maxcolumns = parseInt(this.$selectgridcolumns.find(' option:last-child').val());

	if (!this.options.editing) {
		this.buildThemeGridGrid();
	} else {
		this.$selectgridcolumns.prop('disabled', true);
	}

	this._resetSlider();

	if (this.columns > 1) {
		this._showThemeGridSlider();
	}

}

// This actually takes care of drawing the grid.
CCMLayout.prototype._updateCustomView = function() {

	// load custom view settings
	this.columns = parseInt(this.$selectcolumnscustom.val());
	this.customspacing = this.$customspacing.val();
	this.automatedcustomlayout = this.$customautomated.is(':checked');
	this.columnwidths = [];

	// set relevant forms based on the settings
	if (this.columns < 2) {
		this.$customspacing.prop('disabled', true);
		this.$customautomated.prop('disabled', true);
	} else {
		this.$customspacing.prop('disabled', false);
		this.$customautomated.prop('disabled', false);
	}
	if (this.options.editing) {
		this.$selectcolumnscustom.prop('disabled', true);
	}

	// redraw the content view.
	if (!this.options.editing) {
		this.$element.html('');
	}
	for (i = 0; i < this.columns; i++) {
		if (this.options.editing) {
			if ($('#ccm-edit-layout-column-' + i).length > 0) {
				continue;
			}
		}
		var $column = $('<div />').attr('class', 'ccm-layout-column');
		$column.attr('id', 'ccm-edit-layout-column-' + i);
		var $highlight = $('<div />').attr('class', 'ccm-layout-column-highlight');
		$highlight.append($('<input />', {'name': 'width[' + i + ']', 'type': 'hidden', 'id': 'ccm-edit-layout-column-width-'+ i}));
		$column.append($highlight);
		this.$element.append($column);
	}

	// now we remove unused columns
	var $columns = this.$element.find('.ccm-layout-column');
	if (this.columns < $columns.length) {
		for (i = columns; i < $columns.length; i++ ){
			$('#ccm-edit-layout-column-' + i).remove();
		}
	}

	// now we handle spacing
	for (i = 0; i < this.columns; i++) {
		$highlight = $('#ccm-edit-layout-column-' + i + ' .ccm-layout-column-highlight');
		if (i > 0) {
			$highlight.css('margin-left', (this.customspacing / 2) + 'px');
		}
		if ((i + 1) < this.columns) {
			$highlight.css('margin-right', (this.customspacing / 2) + 'px');
		}
		$column = $('#ccm-edit-layout-column-' + i);
		if ($column.attr('data-width')) {
			var width = $column.attr('data-width') + 'px';
			this.columnwidths.push(parseInt($column.attr('data-width')));
		} else {
			var width = (100 / this.columns) + '%';
		}
		$column.css('width', width);		
	}

	this._resetSlider();

	if ((!this.automatedcustomlayout) && this.columns > 1) {
		this._showCustomSlider();
	}
}

CCMLayout.prototype._resetSlider = function() {
	if (this.$slider) {
		this.$slider.slider('destroy');
	}

	if ($("#ccm-area-layout-active-control-bar").hasClass('ccm-area-layout-control-bar-add')) {
		$('#ccm-area-layout-active-control-bar').css('height', '0px');
	}
}

CCMLayout.prototype._getThemeGridColumnSpan = function(totalColumns) {
	var rowspan = Math.ceil(this.maxcolumns / totalColumns);
	// create the starting array
	var spanArray = [];
	for (i = 0; i < totalColumns; i++) {
		spanArray[i] = rowspan;
	}
	var rowspantotal = rowspan * totalColumns;
	for (i = 0; i < (rowspantotal - this.maxcolumns); i++) {
		var index = spanArray.length - i - 1;
		spanArray[index]--;
	}

	var cssclasses = [];
	for (i = 0; i < spanArray.length; i++) {
		cssclasses[i] = {};
		cssclasses[i].cssClass = this.options.gridColumnClasses[spanArray[i]-1];
		cssclasses[i].value = spanArray[i];
	}
	return cssclasses;
}

CCMLayout.prototype._getThemeGridNearestValue = function(value, values) {
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

CCMLayout.prototype._showThemeGridSlider = function() {

	var obj = this;

	obj.$slider = $('#ccm-area-layout-active-control-bar');
	obj.$slider.css('height', '12px');

	// set the breakpoints
	var breaks = [];
	for (i = 0; i < obj.columns; i++) {
		$column = $('#ccm-edit-layout-column-' + i);
		if (i == 0) {
			// this is the first column so we only get the end
			breaks.push(parseInt($column.width()));
		} else if ((i + 1) == obj.columns) {
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

	var maxColumns = obj.options.maxcolumns;
	var minColumnClass = obj.options.gridColumnClasses[0];

	$('<div />', {'id': obj.options.gridrowtmpid}).appendTo(document.body);
	var columnHTML = '';
	for (i = 1; i <= maxColumns; i++) {
		columnHTML += '<div class="' + minColumnClass + '"></div>'
	}
	$('#' + obj.options.gridrowtmpid).append($(obj.options.rowstart + columnHTML + obj.options.rowend));
	var marginModifier = 0;
	for (i = 0; i < maxColumns; i++) {
		var $column = $($('#' + obj.options.gridrowtmpid + ' .' + minColumnClass).get(i));
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
	$('#' + obj.options.gridrowtmpid).remove();


	obj.$slider.slider({
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

			var oldValue = obj.$slider.slider('values', index);
			var newValue = obj._getThemeGridNearestValue(ui.value, pointsToCheck);
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
				obj.$slider.slider('values', index, newValue);
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
					obj._redrawThemeGrid();
				}
			}
			return false;

		}
	});
}

CCMLayout.prototype._redrawThemeGrid = function() {
	var obj = this;
	obj.$element.find('.ccm-theme-grid-offset-column').remove();
	$.each(obj.$element.find('.ccm-theme-grid-column'), function(i, col) {
		var $col = $(col);
		$col.removeClass().addClass('ccm-theme-grid-column');
		if (obj.options.editing) {
			$col.addClass('ccm-theme-grid-column-edit-mode');
		}
		if ($col.attr('data-span')) {
			var spandex = parseInt($col.attr('data-span')) - 1;
			$col.addClass(obj.options.gridColumnClasses[spandex]);
			// change the span value inside
			$('#ccm-edit-layout-column-span-' + i).val(parseInt($col.attr('data-span')));
		}
		if ($col.attr('data-offset')) {
			var offdex = parseInt($col.attr('data-offset')) - 1;
			$('<div />', {'data-offset-column': true}).addClass('ccm-theme-grid-offset-column').addClass(obj.options.gridColumnClasses[offdex]).insertBefore($col);
			$('#ccm-edit-layout-column-offset-' + i).val(parseInt($col.attr('data-offset')));
		}
	});
}

CCMLayout.prototype._showCustomSlider = function() {

	this.$slider = $('#ccm-area-layout-active-control-bar');
	this.$slider.css('height', '12px');

	var breaks = [],
		sw = 0,
		tw = this.$slider.width(),
		$columns = this.$element.find('.ccm-layout-column');

	if (this.columnwidths.length > 0) {
		// we have custom column widths
		for (i = 0; i < (this.columnwidths.length - 1); i++) {
			sw += this.columnwidths[i];
			breaks.push(sw);
		}
	} else {
		var cw = tw / this.columns;
		for (i = 1; i < this.columns; i++) {
			sw += cw;
			breaks.push(sw);
		}
	}

	this.$slider.slider({
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

}



// public methods
CCMLayout.launchPresets = function(selector, token) {
	jQuery.fn.dialog.open({
		width: 280,
		height: 200,
		modal: false,
		href: CCM_TOOLS_PATH + '/area/layout_presets?ccm_token=' + token,
		title: ccmi18n.areaLayoutPresets, 
		onOpen: function() {
			$('#ccm-layout-save-preset-form select').on('change', function(r) {
				if ($(this).val() == '-1') {
					$('#ccm-layout-save-preset-name').show().focus();
				} else {
					$('#ccm-layout-save-preset-name').hide();
				}
			}).trigger('change');

			$('#ccm-layout-save-preset-form').on('submit', function() {

				$.fn.dialog.showLoader();
				
				var data = $(selector).data('ccmlayout');
				var formdata = data.$toolbar.find('select, input').serializeArray().concat(data.$element.find('input').serializeArray());

				return false;

				/*
				r = $toolbar.serializeArray();
				
				Query.fn.dialog.showLoader();

				jQuery.fn.dialog.closeTop();
				jQuery.fn.dialog.hideLoader();
				return false;

				*/


			});
		}
	});



}
