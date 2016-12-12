/**
 * Free-Form Layouts
 */
(function (global, jQuery) {

    // plugins
    jQuery.fn.concreteLayout = function (options) {
        return this.each(function () {
            var $obj = $(this);
            var data = $obj.data('concreteLayout');
            if (!data) {
                $obj.data('concreteLayout', (data = new ConcreteLayout(this, options)));
            }
        });
    };

    // initialization
    var ConcreteLayout = function (element, options) {
        this.options = $.extend({
            'toolbar': '#ccm-layouts-toolbar',
            'btnsave': '#ccm-layouts-save-button',
            'btncancel': '#ccm-layouts-cancel-button',
            'editing': false,
            'supportsgrid': false,
            'gridrowtmpid': 'ccm-theme-grid-temp',
            'additionalGridColumnClasses': ''
        }, options);

        this.$element = $(element);
        this.$toolbar = $(this.options.toolbar);

        this._setupDOM();
        //this._activatePresets();
        this._setupToolbarView();
        this._setupFormSaveAndCancel();
        this._setupFormEvents();

        this._updateChooseTypeForm();
    };

    // private methods
    ConcreteLayout.prototype._setupDOM = function () {
        // form list items
        this.$formviews = this.$toolbar.find('li[data-grid-form-view]');
        this.$formviewcustom = this.$toolbar.find('li[data-grid-form-view=custom]');
        this.$formviewthemegrid = this.$toolbar.find('li[data-grid-form-view=themegrid]');

        // choosetype option
        this.$selectgridtype = this.$toolbar.find('select[name=gridType]');

        // choosetype + custom
        this.$selectcolumnscustom = this.$toolbar.find('input[type=text][name=columns]');
        this.$customspacing = this.$toolbar.find('input[name=spacing]');
        this.$customautomatedfrm = this.$toolbar.find('input[name=isautomated]');
        this.$customautomated = this.$toolbar.find('[data-layout-button=toggleautomated]');

        // choosetype + themegrid
        this.$selectgridcolumns = this.$toolbar.find('input[type=text][name=themeGridColumns]');

        // all
        this.$savebtn = this.$toolbar.find(this.options.btnsave);
        this.$cancelbtn = this.$toolbar.find(this.options.btncancel);
        this.$slider = false;
    };

    ConcreteLayout.prototype._setupFormSaveAndCancel = function () {
        var obj = this;
        this.$cancelbtn.unbind().on('click', function () {
            obj.$toolbar.remove();
            ConcreteEvent.unsubscribe('EditModeExitInlineComplete.layouts');
            ConcreteEvent.on('EditModeExitInlineComplete.layouts', function (e, data) {
                obj._rescanAreasInPage(e, data);
            });
            ConcreteEvent.fire('EditModeExitInline');
        });
        this.$savebtn.unbind().on('click', function () {
            Concrete.event.fire('EditModeExitInlineSaved');

            // move the toolbar back into the form so it submits. so great.
            obj.$toolbar.hide().prependTo('#ccm-block-form');
            $('#ccm-block-form').submit();
            ConcreteEvent.unsubscribe('EditModeExitInlineComplete.layouts');
            ConcreteEvent.on('EditModeExitInlineComplete.layouts', function (e, data) {
                obj._rescanAreasInPage(e, data);
            });
        });
    };

    ConcreteLayout.prototype._rescanAreasInPage = function (e, data) {
        var editor = Concrete.getEditMode();
        editor.reset();
        editor.scanBlocks();
    };

    ConcreteLayout.prototype._setupToolbarView = function () {
        var obj = this;
        this.$formviews.each(function (i) {
            if ($(this).attr('data-grid-form-view') != obj.options.formview) {
                $(this).hide();
            }
        });
    };

    ConcreteLayout.prototype._updateChooseTypeForm = function () {
        var typeval = this.$selectgridtype.find('option:selected').val();
        var obj = this;
        if (obj.options.editing) {
            obj.$selectgridtype.prop('disabled', true);
        }
        switch (typeval) {
            case 'FF':
                this.$formviewthemegrid.hide();
                this.$formviewcustom.show();
                this._updateCustomView();
                break;
            case 'TG':
                this.$formviewcustom.hide();
                this.$formviewthemegrid.show();
                this._updateThemeGridView();
                break;
            default: // a preset
                if (this.options.editing) {
                    return;
                }
                var arLayoutPresetID = typeval;
                this._resetSlider();
                jQuery.fn.dialog.showLoader();
                var url = CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/area/layout/presets/get/' + CCM_CID + '/' + arLayoutPresetID;
                $.getJSON(url, function (r) {
                    obj.$formviewthemegrid.hide();
                    obj.$formviewcustom.hide();
                    obj.$element.html(r.html);

                    obj.$element.append($('<input />', {
                        'name': 'arLayoutPresetID',
                        'type': 'hidden',
                        'value': arLayoutPresetID
                    }));

                    jQuery.fn.dialog.hideLoader();
                });
                break;
        }
    };


    ConcreteLayout.prototype._setupFormEvents = function () {
        var obj = this;
        this.$selectcolumnscustom.on('keyup', function () {
            obj._updateCustomView();
        });
        this.$customspacing.on('keyup', function () {
            obj._updateCustomView();
        });
        this.$customautomatedfrm.on('change', function () {
            obj._updateCustomView();
        });
        this.$customautomated.on('click', function () {
            if ($(this).parent().hasClass('ccm-inline-toolbar-icon-selected')) {
                $(this).parent().removeClass('ccm-inline-toolbar-icon-selected');
                obj.$customautomatedfrm.val(0);
            } else {
                $(this).parent().addClass('ccm-inline-toolbar-icon-selected');
                obj.$customautomatedfrm.val(1);
            }
            obj.$customautomatedfrm.trigger("change");
            return false;
        });
        this.$selectgridcolumns.on('keyup', function () {
            obj._updateThemeGridView();
        });
        this.$selectgridtype.on('change', function () {
            obj._updateChooseTypeForm();
        });
    };

    ConcreteLayout.prototype.buildThemeGridGrid = function () {
        this.$element.html('');

        var row = this.options.rowstart;
        row += '<div id="ccm-theme-grid-edit-mode-row-wrapper">';

        var columnSpans = this._getThemeGridColumnSpan(this.columns);
        $.each(columnSpans, function (i, spanInfo) {
            // get the class at the starting rowspan
            var columnHTML = '<div id="ccm-edit-layout-column-' + i + '" class="' + spanInfo.cssClass + ' ccm-theme-grid-column" data-offset="0" data-span="' + spanInfo.value + '"><div class="ccm-layout-column-highlight"><input type="hidden" id="ccm-edit-layout-column-offset-' + i + '" name="offset[' + i + ']" value="0" /><input type="hidden" id="ccm-edit-layout-column-span-' + i + '" name="span[' + i + ']" value="' + spanInfo.value + '" /></div></div>';
            // now, sometimes we might need to set the next column to a smaller amount
            row += columnHTML;
        });

        row += '</div>';
        row += this.options.rowend;
        this.$element.append(row);
    };

    ConcreteLayout.prototype._updateThemeGridView = function (presetLoad) {

        if (!presetLoad) {
            this.$selectgridtype.find('option[value=TG]').prop('selected', true);
        }

        // load the current elements from forms
        this.columns = parseInt(this.$selectgridcolumns.val());
        this.maxcolumns = parseInt(this.$selectgridcolumns.attr('data-maximum'));

        if (!this.options.editing) {
            this.buildThemeGridGrid();
        } else {
            this.$selectgridcolumns.prop('disabled', true);
        }

        this._resetSlider();

        if (this.columns > 1) {
            this._showThemeGridSlider();
        }
    };

    ConcreteLayout.prototype._buildThemeGridGridFromPresetColumns = function (arLayoutColumns) {
        this.$element.html('');
        var obj = this;
        var row = this.options.rowstart;
        row += '<div id="ccm-theme-grid-edit-mode-row-wrapper">';
        $.each(arLayoutColumns, function (i, column) {
            var columnHTML = '<div id="ccm-edit-layout-column-' + i + '" class="ccm-theme-grid-column" ' +
                'data-offset="' + column.arLayoutColumnOffset + '" data-span="' + column.arLayoutColumnSpan + '"><div class="ccm-layout-column-highlight">' +
                '<input type="hidden" id="ccm-edit-layout-column-offset-' + i + '" name="offset[' + i + ']" value="' + column.arLayoutColumnOffset +
                '" /><input type="hidden" id="ccm-edit-layout-column-span-' + i + '" name="span[' + i + ']" value="' + column.arLayoutColumnSpan +
                '" /></div></div>';

            // now, sometimes we might need to set the next column to a smaller amount
            row += columnHTML;
        });
        row += '</div>';
        row += this.options.rowend;
        this.$element.append(row);

        this.columns = arLayoutColumns.length;
        this.maxcolumns = parseInt(this.$selectgridcolumns.attr('data-maximum'));

        this._resetSlider();
        this._redrawThemeGrid();
        this._showThemeGridSlider();
    };

    // This actually takes care of drawing the grid.
    ConcreteLayout.prototype._updateCustomView = function (presetLoad) {
        // if it's presetLoad, that means we're updating the view from the first time
        // after loading a preset. In which case we don't switch away from presets in the gridtype dropdown
        // Otherwise, we DO switch away to show that we're not going to use that preset.
        if (!presetLoad) {
            this.$selectgridtype.find('option[value=FF]').prop('selected', true);
        }

        // load custom view settings
        this.columns = parseInt(this.$selectcolumnscustom.val());
        this.customspacing = this.$customspacing.val();
        this.automatedcustomlayout = this.$customautomatedfrm.val() == 1;
        this.columnwidths = [];

        // set relevant forms based on the settings
        /*
         if (this.columns < 2) {
         this.$customspacing.prop('disabled', true);
         this.$customautomated.prop('disabled', true);
         } else {
         this.$customspacing.prop('disabled', false);
         this.$customautomated.prop('disabled', false);
         }
         */
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
            $highlight.append($('<input />', {
                'name': 'width[' + i + ']',
                'type': 'hidden',
                'id': 'ccm-edit-layout-column-width-' + i
            }));
            $column.append($highlight);
            this.$element.append($column);
        }

        // now we remove unused columns
        var $columns = this.$element.find('.ccm-layout-column');
        if (this.columns < $columns.length) {
            for (i = columns; i < $columns.length; i++) {
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
    };

    ConcreteLayout.prototype._resetSlider = function () {
        if (this.$slider) {
            this.$slider.slider('destroy');
            this.$slider = false;
        }

        if ($("#ccm-area-layout-active-control-bar").hasClass('ccm-area-layout-control-bar-add')) {
            $('#ccm-area-layout-active-control-bar').css('height', '0px');
        }
    };

    ConcreteLayout.prototype._getThemeGridColumnSpan = function (totalColumns) {
        var rowspan = Math.ceil(this.maxcolumns / totalColumns);
        // create the starting array
        var spanArray = [], i;
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
            cssclasses[i].cssClass = this.options.gridColumnClasses[spanArray[i] - 1];
            if (this.options.additionalGridColumnClasses) {
                cssclasses[i].cssClass = cssclasses[i].cssClass + ' ' + this.options.additionalGridColumnClasses;
            }
            cssclasses[i].value = spanArray[i];
        }
        return cssclasses;
    };

    ConcreteLayout.prototype._getThemeGridNearestValue = function (value, values) {
        var nearest = null;
        var diff = null;
        $.each(values, function () {
            if (nearest == null || Math.abs(this - value) < Math.abs(nearest - value)) {
                nearest = this;
            }
        });

        return nearest;
    };

    ConcreteLayout.prototype._showThemeGridSlider = function () {

        var obj = this;

        obj.$slider = $('#ccm-area-layout-active-control-bar');
        obj.$slider.css('height', '6px');

        // set the breakpoints
        var breaks = [];
        for (i = 0; i < obj.columns; i++) {
            $column = $('#ccm-edit-layout-column-' + i);
            if (i == 0) {
                // this is the first column so we only get the end
                breaks.push(Math.floor($column.width()));
            } else if ((i + 1) == obj.columns) {
                breaks.push(Math.floor($column.position().left));
            } else {
                breaks.push(Math.floor($column.position().left));
                breaks.push(Math.floor($column.width()) + Math.floor($column.position().left));
            }

        }

        // set the valid widths
        var tw = $('#ccm-area-layout-active-control-bar').width();
        var sw = 0;
        var validStartPoints = [];
        var validEndPoints = [];

        var maxColumns = obj.options.maxcolumns;
        var minColumnClass = obj.options.gridColumnClasses[0];

        var test_container = $('#ccm-theme-grid-edit-mode-row-wrapper').closest('.ccm-block-edit-layout, .ccm-layouts-edit-mode-add');
        if (!test_container.length) {
            test_container = $('#ccm-theme-grid-edit-mode-row-wrapper');
        }

        var test_container_div = $('<div />', {'id': obj.options.gridrowtmpid}).appendTo(test_container);
        var columnHTML = '';
        for (i = 1; i <= maxColumns; i++) {
            if (obj.options.additionalGridColumnClasses) {
                columnHTML += '<div class="' + minColumnClass + ' ' + obj.options.additionalGridColumnClasses + '"><br><br></div>'
            } else {
                columnHTML += '<div class="' + minColumnClass + '"><br><br></div>'
            }
        }


        var grid_elem = obj.options.rowstart
            + columnHTML
            + obj.options.rowend;

        test_container_div.append(
            $(grid_elem)
        );
        var marginModifier = 0;
        for (var i = 0; i < maxColumns; i++) {
            var $column = test_container_div.find('.' + minColumnClass).eq(i);
            if (i == 0) {
                var pl = $column.position().left;
                if (pl < 0) {
                    marginModifier = Math.abs(pl); // handle stupid grids that have negative margin starters
                }
            }
            // handle the START of every column
            validStartPoints.push(Math.floor($column.position().left + marginModifier));

            // handle the END of every column
            validEndPoints.push(Math.floor(Math.floor($column.width()) + Math.floor($column.position().left) + marginModifier));
        }
        test_container_div.remove();

        obj.$slider.slider({
            min: 0,
            max: tw,
            step: 1,
            values: breaks,

            slide: function (e, ui) {

                if (obj.$selectgridtype.val() != 'TG') {
                    obj.$selectgridtype.find('option[value=TG]').prop('selected', true);
                }

                var index = $(ui.handle).index();
                var pointsToCheck;

                if ((index % 2) == 0) {
                    pointsToCheck = validEndPoints;
                } else {
                    pointsToCheck = validStartPoints;
                }

                // now we normalize the pointsToCheck – we go through each value in the breaks
                // array and we ensure that the corresponding value in the pointsToCheck array
                // actually matches exactly.
                for (var x = 0; x < breaks.length; x++) {
                    for (var y = 0; y < pointsToCheck.length; y++) {
                        var diff = Math.abs(breaks[x] - pointsToCheck[y]);
                        if (diff <= 2) {
                            pointsToCheck[y] = breaks[x];
                        }
                    }
                }

                var oldValue = obj.$slider.slider('values', index);
                var newValue = obj._getThemeGridNearestValue(ui.value, pointsToCheck);

                // now we determine whether we CAN go there or is it going to encroach upon another point.
                var proceed = true;
                $.each(ui.values, function (i, value) {
                    if (newValue >= value && index < i) {
                        proceed = false;
                    } else if (newValue <= value && index > i) {
                        proceed = false;
                    }
                });

                // now we only proceed if proceed is set to true and the values don't match
                if (proceed && oldValue == newValue) {
                    proceed = false;
                }

                if (proceed) {
                    obj.$slider.slider('values', index, newValue);
                    if ((index % 2) == 0) {
                        i = Math.floor(index / 2);
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
                return false;

            }
        });
    };

    ConcreteLayout.prototype._redrawThemeGrid = function () {
        var obj = this;
        obj.$element.find('.ccm-theme-grid-offset-column').remove();
        $.each(obj.$element.find('.ccm-theme-grid-column'), function (i, col) {
            var $col = $(col);
            $col.removeClass();
            if ($col.attr('data-span')) {
                var spandex = parseInt($col.attr('data-span')) - 1;
                $col.addClass(obj.options.gridColumnClasses[spandex]);
                if (obj.options.additionalGridColumnClasses) {
                    $col.addClass(obj.options.additionalGridColumnClasses);
                }
                // change the span value inside
            }
            if (parseInt($col.attr('data-offset')) > 0) {
                var offdex = parseInt($col.attr('data-offset')) - 1;
                var offsetColumnClass = obj.options.gridColumnClasses[offdex] + ' ccm-theme-grid-offset-column';
                if (obj.options.additionalGridColumnOffsetClasses) {
                    offsetColumnClass = offsetColumnClass + ' ' + obj.options.additionalGridColumnOffsetClasses;
                }
                $('<div />', {'data-offset-column': true}).html('&nbsp;').addClass(offsetColumnClass).insertBefore($col);
            }
            $('#ccm-edit-layout-column-offset-' + i).val(parseInt($col.attr('data-offset')));
            $('#ccm-edit-layout-column-span-' + i).val(parseInt($col.attr('data-span')));
            $col.addClass('ccm-theme-grid-column');
            if (obj.options.editing) {
                $col.addClass('ccm-theme-grid-column-edit-mode');
            }
        });
    };

    ConcreteLayout.prototype._showCustomSlider = function () {

        this.$slider = $('#ccm-area-layout-active-control-bar');
        this.$slider.css('height', '6px');

        var breaks = [],
            sw = 0,
            tw = Math.floor(this.$slider.width()),
            $columns = this.$element.find('.ccm-layout-column'),
            i;

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

        var obj = this;

        this.$slider.slider({
            min: 0,
            max: tw,
            step: 1,
            values: breaks,
            create: function (e, ui) {
                var createoffset = 0;
                var breakwidths = [];

                $.each($columns, function (i, col) {
                    var bw = breaks[i], value;
                    if ((i + 1) == $columns.length) {
                        // last column
                        value = tw - createoffset;
                    } else {
                        value = bw - createoffset;
                    }
                    value = Math.floor(value);
                    $(col).find('#ccm-edit-layout-column-width-' + i).val(value);
                    createoffset = bw;
                });
            },

            slide: function (e, ui) {

                if (obj.$selectgridtype.val() != 'FF') {
                    obj.$selectgridtype.find('option[value=FF]').prop('selected', true);
                }

                var lastvalue = 0,
                    proceed = true;

                $.each(ui.values, function (i, value) {
                    if (value < lastvalue) {
                        proceed = false;
                    }
                    lastvalue = value;
                });

                if (proceed) {
                    lastvalue = 0;
                    $.each($columns, function (i, col) {
                        var value;

                        if ((i + 1) == $columns.length) {
                            // last column
                            value = tw - lastvalue;
                        } else {
                            value = ui.values[i] - lastvalue;
                        }
                        value = Math.floor(value);
                        $(col).find('#ccm-edit-layout-column-width-' + i).val(value);
                        $(col).css('width', value + 'px');
                        lastvalue = ui.values[i];
                    });
                } else {
                    return false;
                }
            }
        });

    };
}(this, jQuery));
