/* jshint unused:vars, undef:true, browser:true, jquery:true */
(function(global, $) {
'use strict';

var $form, i18n;

function buildTextarea(value) {
	return $('<textarea class="form-control" style="width: 100%; height: 400px; font-family: Menlo, Monaco, Consolas, \'Courier New\', monospace; font-size: 0.9em; white-space: nowrap; overflow: auto;" />')
		.val(typeof value === 'string' ? value : '');
}

var Parsers = {
	color: {
		serialize: function($form, serialized) {
			$.each($form.find('input'), function() {
				var $input = $(this);
				if (!/.\[color\]$/.test($input.attr('name'))) {
					return;
				}
				serialized.push({key: $input.attr('name'), value: $input.val()});
			});
		},
		unserialize: function($form, str) {
			var m = /^(.+\[color\])\s*:(.*)$/.exec(str);
			if (!m) {
				return false;
			}
			var name = $.trim(m[1]), value = $.trim(m[2]);
			var $input = $form.find('input[name="' + name + '"]');
			if ($input.length !== 1) {
				return;
			}
			$input.spectrum('set', value);
		}
	},
	fontFamily: {
		serialize: function($form, serialized) {
			$.each($form.find('input'), function() {
				var $input = $(this);
				if (!/.\[font-family\]$/.test($input.attr('name'))) {
					return;
				}
				serialized.push({key: $input.attr('name'), value: $input.val()});
			});
		}
	},
	fontWeight: {
		serialize: function($form, serialized) {
			$.each($form.find('input'), function() {
				var $input = $(this);
				if (!/.\[font-weight\]$/.test($input.attr('name'))) {
					return;
				}
				serialized.push({key: $input.attr('name'), value: $input.val()});
			});
		}
	},
	image: {
		serialize: function($form, serialized) {
			$.each($form.find('input'), function() {
				var $input = $(this);
				if (!/.\[image\]$/.test($input.attr('name'))) {
					return;
				}
				serialized.push({key: $input.attr('name'), value: $input.val()});
			});
		},
		unserialize: function($form, str) {
			var m = /^(.+\[image\])\s*:(.*)$/.exec(str);
			if (!m) {
				return false;
			}
			var name = $.trim(m[1]), value = $.trim(m[2]);
			var $input = $form.find('input[name="' + name + '"]');
			if ($input.length !== 1) {
				return;
			}
			$input.val(value);
		}
	},
	sizedUnit: {
		serialize: function($form, serialized) {
			$.each($form.find('input'), function() {
				var $size = $(this), name = $size.attr('name');
				if (!/.\[size\]$/.test(name)) {
					return;
				}
				name = name.substr(0, name.length - '[size]'.length);
				var $unit = $form.find('input[name="' + name + '[unit]"]');
				if ($unit.length !== 1) {
					return;
				}
				serialized.push({key: name, value: $size.val() + $unit.val()});
			});
		}
	},
	textProp: {
		serialize: function($form, serialized) {
			$.each($form.find('input'), function() {
				var $input = $(this);
				if (!/.\[(italic|underline|uppercase)\]$/.test($input.attr('name'))) {
					return;
				}
				serialized.push({key: $input.attr('name'), value: parseInt($input.val()) ? 'yes' : 'no'});
			});
		}
	}
};

function exportValues() {
	var serialized = [];

	$.each(Parsers, function() {
		if (this.serialize) {
			this.serialize($form, serialized);
		}
	});
	serialized.sort(function(a, b) {
		if (a.key < b.key) {
			return -1;
		}
		if (a.key > b.key) {
			return 1;
		}
		return 0;
	});
	var result = '';
	$.each(serialized, function() {
		result += this.key + ': ' + this.value + '\n';
	});

	return result;
}
function showExportValues() {
	var str = exportValues();

	var $input;
	var $dialog = $('<div class="ccm-ui" />');
	$dialog
		.append($input = buildTextarea(str))
		.append($('<div class="dialog-buttons" />')
			.append($('<button class="btn btn-primary pull-left" />')
				.text(i18n.Close)
				.on('click', function() {
					$dialog.dialog('close');
				})
			)
		)
	;
	$(document.body).append($dialog);
	$.fn.dialog.open({
		element: $dialog,
		modal: true,
		width: 600,
		height: 'auto',
		title: i18n.CurrentThemeValues,
		open: function() {
			$input.focus().select();
		},
		close: function() {
			$dialog.remove();
		}
	});
}

function showImportValues() {
	var $input;
	var $dialog = $('<div class="ccm-ui" />');
	$dialog
		.append($input = buildTextarea())
		.append($('<div class="dialog-buttons" />')
			.append($('<button class="btn btn-default pull-left" />')
				.text(i18n.Cancel)
				.on('click', function() {
					$dialog.dialog('close');
				})
			)
			.append($('<button class="btn btn-primary pull-right" />')
				.text(i18n.Apply)
				.on('click', function() {
					var str = $.trim($input.val());
					if (str.length === 0) {
						$input.val('').focus();
						return;
					}
					importValues(str);
					$dialog.dialog('close');
				})
			)
		)
	;
	$.fn.dialog.open({
		element: $dialog,
		modal: true,
		width: 600,
		height: 'auto',
		title: i18n.NewThemeValues,
		open: function() {
			$input.focus().select();
		},
		close: function() {
			$dialog.remove();
		}
	});
}

function importValues(str) {
	var lines = str.replace(/\r\n/g, '\n').replace(/\r/g, '\n').split('\n');
	var notUnserializedLines = [], someUnserializedLines = false;
	$.each(lines, function(lineIndex, line) {
		line = $.trim(line);
		if (line.length === 0) {
			return;
		}
		var unserialized = false;
		$.each(Parsers, function() {
			if (this.unserialize) {
				if (this.unserialize($form, line)) {
					unserialized = true;
					return false;
				}
			}
		});
		if (unserialized === true) {
			someUnserializedLines = true;
		} else {
			notUnserializedLines.push({line: lineIndex + 1, text: line});
		}
	});
	if (someUnserializedLines) {
		ConcreteEvent.publish('StyleCustomizerControlUpdate');
	}
	return notUnserializedLines;
}

global.ConcreteStyleCustomizerImportExport = {
	initialize: function(data) {
		i18n = data.i18n;
		$form = $('form[data-form=panel-page-design-customize]');
		$('#ccm-panel-page-design-customize-export').on('click', function() {
			showExportValues();
		});
		$('#ccm-panel-page-design-customize-import').on('click', function() {
			showImportValues();
		});
	}
};

})(this, $);
