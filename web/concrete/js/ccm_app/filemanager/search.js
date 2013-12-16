/**
 * block ajax
 */

!function(global, $) {
	'use strict';

	function ConcreteFileManager($element, options) {
		'use strict';
		var my = this;
		options = $.extend({
			'mode': 'menu'
		}, options);

		my.options = options;
		my._templateSearchResultsMenu = _.template(ConcreteFileManagerMenu.get());

		ConcreteAjaxSearch.call(my, $element, options);

		if (!$('#ccm-file-manager-download-target').length) {
			my.$downloadTarget = $('<iframe />', {'id': 'ccm-file-manager-download-target'}).appendTo(document.body);
		} else {
			my.$downloadTarget = $('#ccm-file-manager-download-target');
		}
	}

	ConcreteFileManager.prototype = Object.create(ConcreteAjaxSearch.prototype);

	ConcreteFileManager.prototype.setupStarredResults = function() {
		var my = this;
		my.$element.unbind('.concreteFileManagerStar').on('click.concreteFileManagerStar', 'a[data-search-toggle=star]', function() {
			var $link = $(this);
			var data = {'fID': $(this).attr('data-search-toggle-file-id')};
			my.ajaxUpdate($link.attr('data-search-toggle-url'), data, function(r) {
				if (r.star) {
					$link.parent().addClass('ccm-file-manager-search-results-star-active');
				} else {
					$link.parent().removeClass('ccm-file-manager-search-results-star-active');	
				}
			});
			return false;
		});
	}

	ConcreteFileManager.prototype.updateResults = function(result) {
		var my = this;
		ConcreteAjaxSearch.prototype.updateResults.call(my, result);
		my.setupStarredResults();
		if (my.options.mode == 'choose') {
			my.$element.unbind('.concreteFileManagerHoverFile');
			my.$element.on('mouseover.concreteFileManagerHoverFile', 'tr[data-file-manager-file]', function() {
				$(this).addClass('ccm-search-select-hover');
			});
			my.$element.on('mouseout.concreteFileManagerHoverFile', 'tr[data-file-manager-file]', function() {
				$(this).removeClass('ccm-search-select-hover');
			});
			my.$element.unbind('.concreteFileManagerChooseFile').on('click.concreteFileManagerChooseFile', 'tr[data-file-manager-file]', function() {
				ConcreteEvent.publish('FileManagerSelectFile', {fID: $(this).attr('data-file-manager-file')});
				return false;
			});
		}
	}

	ConcreteFileManager.prototype.handleSelectedBulkAction = function(value, type, $option, $items) {
		var my = this, itemIDs = [];
		$.each($items, function(i, checkbox) {
			itemIDs.push({'name': 'item[]', 'value': $(checkbox).val()});
		});

		if (value == 'download') {
			my.$downloadTarget.get(0).src = CCM_TOOLS_PATH + '/files/download?' + jQuery.param(itemIDs);
		} else {
			ConcreteAjaxSearch.prototype.handleSelectedBulkAction.call(this, value, type, $option, $items);
		}
	}

	ConcreteAjaxSearch.prototype.createMenu = function($selector) {
		var my = this;
		$selector.concreteFileMenu({
			'container': my,
			'menu': $('[data-search-menu=' + $selector.attr('data-launch-search-menu') + ']')
		});
	}

	var ConcreteFileManagerMenu = {

		get: function() {
			return '<div class="ccm-ui"><div class="ccm-popover-file-menu popover fade" data-search-file-menu="<%=item.fID%>" data-search-menu="<%=item.fID%>">' +
				'<div class="arrow"></div><div class="popover-inner"><ul class="dropdown-menu">' + 
				'<% if (typeof(displayClear) != \'undefined\' && displayClear) { %>' +
				'<li><a href="#" data-file-manager-action="clear">' + ccmi18n_filemanager.clear + '</a></li>' +
				'<li class="divider"></li>' +
				'<% } %>' +
				'<% if (item.canViewFile) { %>' + 
					'<li><a class="dialog-launch" dialog-modal="false" dialog-append-buttons="true" dialog-width="90%" dialog-height="75%" dialog-title="' + ccmi18n_filemanager.view + '" href="' + CCM_TOOLS_PATH + '/files/view?fID=<%=item.fID%>">' + ccmi18n_filemanager.view + '</a></li>' +
				'<% } %>' +
				'<li><a href="#" onclick="window.frames[\'ccm-file-manager-download-target\'].location=\'' + CCM_TOOLS_PATH + '/files/download?fID=<%=item.fID%>\'; return false">' + ccmi18n_filemanager.download + '</a></li>' +
				'<% if (item.canEditFile) { %>' + 
					'<li><a class="dialog-launch" dialog-modal="true" dialog-width="680" dialog-height="450" dialog-title="' + ccmi18n_filemanager.edit + '" href="' + CCM_TOOLS_PATH + '/files/edit?fID=<%=item.fID%>">' + ccmi18n_filemanager.edit + '</a></li>' +
				'<% } %>' +
				'<li><a class="dialog-launch" dialog-modal="true" dialog-width="680" dialog-height="450" dialog-title="' + ccmi18n_filemanager.properties + '" href="' + CCM_TOOLS_PATH + '/files/properties?fID=<%=item.fID%>">' + ccmi18n_filemanager.properties + '</a></li>' +
				'<% if (item.canReplaceFile) { %>' + 
					'<li><a class="dialog-launch" dialog-modal="true" dialog-width="300" dialog-height="260" dialog-title="' + ccmi18n_filemanager.replace + '" href="' + CCM_TOOLS_PATH + '/files/replace?fID=<%=item.fID%>">' + ccmi18n_filemanager.replace + '</a></li>' +
				'<% } %>' +
				'<% if (item.canCopyFile) { %>' + 
					'<li><a href="#" data-file-manager-action="duplicate">' + ccmi18n_filemanager.duplicate + '</a></li>' +
				'<% } %>' +
				'<li><a class="dialog-launch" dialog-modal="true" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.sets + '" href="' + CCM_TOOLS_PATH + '/files/add_to?fID=<%=item.fID%>">' + ccmi18n_filemanager.sets + '</a></li>' +
				'<% if (item.canDeleteFile || item.canEditFilePermissions) { %>' + 
					'<li class="divider"></li>' +
				'<% } %>' +
				'<% if (item.canEditFilePermissions) { %>' + 
					'<li><a class="dialog-launch" dialog-modal="true" dialog-width="400" dialog-height="450" dialog-title="' + ccmi18n_filemanager.permissions + '" href="' + CCM_TOOLS_PATH + '/files/permissions?fID=<%=item.fID%>">' + ccmi18n_filemanager.permissions + '</a></li>' +
				'<% } %>' +
				'<% if (item.canDeleteFile) { %>' + 
				'<li><a class="dialog-launch" dialog-modal="true" dialog-width="500" dialog-height="200" dialog-title="' + ccmi18n_filemanager.deleteFile + '" href="' + CCM_TOOLS_PATH + '/files/delete?fID=<%=item.fID%>">' + ccmi18n_filemanager.deleteFile + '</a></li>' +
				'<% } %>' +
			'</ul></div></div>';
		}
	}

	// jQuery Plugin
	$.fn.concreteFileManager = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteFileManager($(this), options);
		});
	}

	global.ConcreteFileManager = ConcreteFileManager;
	global.ConcreteFileManagerMenu = ConcreteFileManagerMenu;

}(this, $);