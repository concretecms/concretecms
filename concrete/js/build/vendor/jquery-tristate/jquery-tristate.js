/*jslint devel: true, bitwise: true, regexp: true, browser: true, confusion: true, unparam: true, eqeq: true, white: true, nomen: true, plusplus: true, maxerr: 50, indent: 4 */
/*globals jQuery */

/*!
 * Tristate
 *
 * Copyright (c) 2013-2014 Martijn W. van der Lee
 * Licensed under the MIT.
 */
/* Based on work by:
 *  Chris Coyier (http://css-tricks.com/indeterminate-checkboxes/)
 *
 * Tristate checkbox with support features
 * pseudo selectors
 * val() overwrite
 */

(function($){
	'use strict';

	 var pluginName	= 'vanderlee.tristate',
		originalVal = $.fn.val;

	$.widget("vanderlee.tristate", {
		options: {
			state:				undefined,
			value:				undefined,	// one-way only!
			checked:			undefined,
			unchecked:			undefined,
			indeterminate:		undefined,

			change:				undefined,
			init:				undefined
		},

		_create: function() {
			var that = this,
				state;

			this.element.click(function(e) {
				if (e.isTrigger || !e.hasOwnProperty('which')) {
					e.preventDefault();
				}
				
				switch (that.options.state) {
					case true:  that.options.state = null; break;
					case false: that.options.state = true; break;
					default:    that.options.state = false; break;
				}

				that._refresh(that.options.change);
			});

			this.options.checked		= this.element.attr('checkedvalue')		  || this.options.checked;
			this.options.unchecked		= this.element.attr('uncheckedvalue')	  || this.options.unchecked;
			this.options.indeterminate	= this.element.attr('indeterminatevalue') || this.options.indeterminate;

			// Initially, set state based on option state or attributes
			if (typeof this.options.state === 'undefined') {
				this.options.state		= typeof this.element.attr('indeterminate') !== 'undefined'? null : this.element.is(':checked');
			}

			// If value specified, overwrite with value
			if (typeof this.options.value !== 'undefined') {
				state = this._parseValue(this.options.value);
				if (typeof state !== 'undefined') {
					this.options.state = state;
				}
			}

			this._refresh(this.options.init);

			return this;
		},

		_refresh: function(callback) {
			var value	= this.value();

			this.element.data(pluginName, value);

			this.element[this.options.state === null ? 'attr' : 'removeAttr']('indeterminate', 'indeterminate');
			this.element.prop('indeterminate', this.options.state === null);
			this.element.get(0).indeterminate = this.options.state === null;

			this.element[this.options.state ? 'attr' : 'removeAttr']('checked', true);
			this.element.prop('checked', this.options.state === true);

			if ($.isFunction(callback)) {
				callback.call(this.element, this.options.state, this.value());
			}
		},

		state: function(value) {
			if (typeof value === 'undefined') {
				return this.options.state;
			} else if (value === true || value === false || value === null) {
				this.options.state = value;

				this._refresh(this.options.change);
			}
			return this;
		},

		_parseValue: function(value) {
			if (value === this.options.checked) {
				return true;
			} else if (value === this.options.unchecked) {
				return false;
			} else if (value === this.options.indeterminate) {
				return null;
			}
		},

		value: function(value) {
			if (typeof value === 'undefined') {
				var value;
				switch (this.options.state) {
					case true:
						value = this.options.checked;
						break;

					case false:
						value = this.options.unchecked;
						break;

					case null:
						value = this.options.indeterminate;
						break;
				}
				return typeof value === 'undefined'? this.element.attr('value') : value;
			} else {
				var state = this._parseValue(value);
				if (typeof state !== 'undefined') {
					this.options.state = state;
					this._refresh(this.options.change);
				}
			}
		}
	});

	// Overwrite fn.val
    $.fn.val = function(value) {
        var data = this.data(pluginName);
        if (typeof data === 'undefined') {
	        if (typeof value === 'undefined') {
	            return originalVal.call(this);
			} else {
				return originalVal.call(this, value);
			}
		} else {
	        if (typeof value === 'undefined') {
				return data;
			} else {
				this.data(pluginName, value);
				return this;
			}
		}
    };

	// :indeterminate pseudo selector
    $.expr.filters.indeterminate = function(element) {
		var $element = $(element);
		return typeof $element.data(pluginName) !== 'undefined' && $element.prop('indeterminate');
    };

	// :determinate pseudo selector
    $.expr.filters.determinate = function(element) {
		return !($.expr.filters.indeterminate(element));
    };

	// :tristate selector
    $.expr.filters.tristate = function(element) {
		return typeof $(element).data(pluginName) !== 'undefined';
    };
}(jQuery));
