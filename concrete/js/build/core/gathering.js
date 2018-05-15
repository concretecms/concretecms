/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global CCM_CID, CCM_TOOLS_PATH, ConcreteMenu */

;(function(global, $) {
  'use strict';

  var methods = {

    private:  {

    	handleAppendedElements: function(response, $gathering, options, prepend) {
			var elements = ($('<div />').append(response).find('>div'));
			$.each(elements, function(i, obj) {
				if(prepend) {
				    $gathering.prepend(obj);
				} else {
				    $gathering.append(obj);
				}
			});
			if (elements.length > 0) {
			    if (prepend) {
			        $gathering.packery('prepended', elements);
			    } else {
			        $gathering.packery('appended', elements);
			    }
				if (options.showTileControls) {
					methods.private.enableEditing($gathering, options);
				}

				methods.private.enableOverlay($gathering, options);
				methods.private.enableHover($gathering, options);
			}
    	},

    	enableOverlay: function($gathering, options) {
			$gathering.find('a[data-overlay=gathering-item]').not('.overlay-bound').each(function() {
				var gaiID = $(this).closest('[data-gathering-item-id]').attr('data-gathering-item-id');
				$(this).on('click', function() {
					$.magnificPopup.open({
						ajax: {
							settings: {
								'data': {
									'gaiID': gaiID,
									'token': options.loadToken
								},
								'dataType': 'html',
								'type': 'post'
							}
						},
						items: {
							src: CCM_TOOLS_PATH + '/gathering/item/detail'
						},
						mainClass: 'ccm-gathering-overlay-wrapper',	
						type: 'ajax',
						removalDelay: 200
					});
					return false;
				});
			}).addClass('overlay-bound');
    	},

    	enableHover: function($gathering, options) {

			$gathering.find('.ccm-gathering-item').not('.hover-bound').each(function() {
				$(this).on('mouseenter', function() {
					$(this).addClass('ccm-gathering-item-over');
				}).on('mouseleave', function() {
					$(this).removeClass('ccm-gathering-item-over');
				});
			}).addClass('hover-bound');
    	},

    	enableEditing: function($gathering, options) {
			$gathering.find('a[data-inline-command=options-tile]').not('.gathering-options-bound').on('click', function(e) {
				var $item = $(this);
				var $menu = $('[data-menu=' + $(this).attr('data-launch-menu') + ']'); 
				var menu = new ConcreteMenu($item, {
					menu: $menu,
					launcher: 'none'
				});
				menu.show(e);
				return false;
			}).addClass('gathering-options-bound');

			var $itemElements = $($gathering.packery('getItemElements')).not('.event-bound');
			$itemElements.draggable({
				'handle': 'a[data-inline-command=move-tile]', 
				start: function() {
					$('.ccm-area-block-dropzone').addClass('ccm-area-block-dropzone-active');
					$('div[data-gathering-id]').each(function() {
						var $tagg = $(this);
						if (parseInt($tagg.attr('data-gathering-id')) != parseInt(options.gaID)) {
							$tagg.addClass('ccm-gathering-item-droppable').droppable({
								accept: '.ccm-gathering-item',
								tolerance: 'pointer',
								hoverClass: 'ccm-gathering-item-drop-active',
								drop: function(e, ui) {
									jQuery.fn.dialog.showLoader();
									var $destination = $(this);
									//var gaID = $destination.attr('data-gathering-id');
									var data = [
										{'name': 'task', 'value': 'move_to_new_gathering'},
										{'name': 'gaiID', 'value': ui.draggable.attr('data-gathering-item-id')},
										{'name': 'gaID', 'value': $destination.attr('data-gathering-id')},
										{'name': 'cID', 'value': CCM_CID},
										{'name': 'itemsPerPage', 'value': options.itemsPerPage},
										{'name': 'editToken', 'value': options.editToken}
									];

									var $source = $(ui.draggable).parent();
									$(ui.draggable).remove();

									$.ajax({
										type: 'post',
										url: CCM_TOOLS_PATH + '/gathering/update',
										data: data,
										success: function(r) {
											$source.packery('layout');
											$destination.before(r).remove();
											$destination.packery('layout');
											jQuery.fn.dialog.hideLoader();
										}
									});
								}
							});
						}
					});
				},
				stop: function() {
					//$('div.ccm-gathering-item-droppable').droppable('destroy');
					$('.ccm-area-block-dropzone').removeClass('ccm-area-block-dropzone-active');
					$gathering.packery('layout');
				}
			});

			$gathering.packery('on', 'dragItemPositioned', function(pkr, item) {
				var data = [
					{'name': 'task', 'value': 'update_display_order'},
					{'name': 'gaID', 'value': options.gaID},
					{'name': 'editToken', 'value': options.editToken}
				];
				var elements = pkr.getItemElements();
				for (var i = 0; i < elements.length; i++) {
					var $obj = $(elements[i]);
					data.push({'name': 'gaiID[]', 'value': $obj.attr('data-gathering-item-id')});
				}

				$.ajax({
					type: 'post',
					url: CCM_TOOLS_PATH + '/gathering/update',
					data: data
				});
			});

			$gathering.packery( 'bindUIDraggableEvents', $itemElements );
			$itemElements.resizable({
				handles: 'se',
				helper: 'ccm-gathering-resize-helper',
				grid: [options.columnWidth + options.gutter, options.rowHeight + options.gutter],
				stop: function(e, ui) {
					var $tile = ui.element,
					wx = parseInt($tile.css('width')),
					hx = parseInt($tile.css('height')),
					w = Math.floor(wx / options.columnWidth),
					h = Math.floor(hx / options.rowHeight);

					$gathering.packery('layout');

					$.ajax({
						type: 'post',
						url: CCM_TOOLS_PATH + '/gathering/update',
						data: {
							'task': 'resize',
							'gaID': options.gaID,
							'gaiID': $tile.attr('data-gathering-item-id'),
							'gaiSlotWidth': w,
							'gaiSlotHeight': h,
							'editToken': options.editToken
						}
					});
				}
			});
			$itemElements.not('.event-bound').addClass('event-bound');
    	}

    },
   
    updateItemTemplate: function(options) {
		jQuery.fn.dialog.showLoader();
		options = $.extend({
			reloadItemTile: false
		}, options);
		$.ajax({
			type: 'POST',
			data: {
				task: 'update_item_template',
				gaiID: options.gaiID,
				gatTypeID: options.gatTypeID,
				gatID: options.gatID,
				token: options.updateToken
			},
			url: CCM_TOOLS_PATH + '/gathering/item/template',
			success: function(r) {
				jQuery.fn.dialog.hideLoader();
				if (options.reloadItemTile) {
					// load the newly rendered HTML into the old gathering item.
					$('[data-gathering-item-id=' + options.gaiID + ']').find('div.ccm-gathering-item-inner-render').html(r);
				}
				jQuery.fn.dialog.closeTop();
			}
		});
    },

    deleteItem: function(options) {
		jQuery.fn.dialog.showLoader();
		$.ajax({
			type: 'POST',
			data: {
				task: 'delete_item',
				gaiID: options.gaiID,
				token: options.deleteToken
			},
			url: CCM_TOOLS_PATH + '/gathering/item/delete',
			success: function(r) {
				jQuery.fn.dialog.hideLoader();
				var $item = $('[data-gathering-item-id=' + options.gaiID + ']');
				var $gathering = $item.parent();
				$item.remove();
				$gathering.packery('layout');
				jQuery.fn.dialog.closeTop();
			}
		});
    },

    getNew: function() {
    	var $gathering = $(this);
		var options = $(this).data('options');
		jQuery.fn.dialog.showLoader();
		var getNewerThan = $($gathering.find('.ccm-gathering-item')[0]).attr('data-gathering-item-id');
		$.ajax({
			type: 'post',
			url: CCM_TOOLS_PATH + '/gathering/get_new',
			data: {
				'task': 'get_gathering_items',
				'newerThan': getNewerThan,
				'gaID': options.gaID,
				'editToken': options.editToken,
				'showTileControls': options.showTileControls
			},
		
			success: function(r) {
				jQuery.fn.dialog.hideLoader();
				methods.private.handleAppendedElements(r, $gathering, options, true);
			}
		});
    },

	init: function(options) {

		options = $.extend({
			totalPages: 0,
			columnWidth: 120,
			itemsPerPage: 24,
			rowHeight: 120,
			showTileControls: false,
			gutter: 1
		}, options);

		return this.each(function() {
			var $gathering = $(this);
			$(this).data('options', options);
			var $loadButton = $gathering.parent().find('button[data-gathering-button=gathering-load-more-items]');
			if (options.totalPages == 1) {
				$loadButton.hide();
			}
			$gathering.packery({
				columnWidth: options.columnWidth,
				rowHeight: options.rowHeight,
				gutter: options.gutter
			});
			$gathering.css('opacity', 1);

			// handle details and lightbox.
			methods.private.enableHover($gathering, options);

			// handle details and lightbox.
			methods.private.enableOverlay($gathering, options);

			$loadButton.on('click', function() {
				var page = parseInt($gathering.attr('data-gathering-current-page')),
					newPage = page + 1;

				$loadButton.prop('disabled', true);

				$.ajax({
					type: 'post',
					url: CCM_TOOLS_PATH + '/gathering/load_more',
					data: {
						'task': 'get_gathering_items',
						'gaID': options.gaID,
						'page': newPage,
						'itemsPerPage': options.itemsPerPage,
						'loadToken': options.loadToken,
						'editToken': options.editToken,
						'showTileControls': options.showTileControls
					},
				
					success: function(r) {
						methods.private.handleAppendedElements(r, $gathering, options);
						if (newPage == options.totalPages) {
							$loadButton.hide();
						} else {
							$loadButton.prop('disabled', false);
							$gathering.attr('data-gathering-current-page', newPage);
						}
					}
				});
			});

			if (options.showTileControls) {
				var $refreshButton = $('[data-gathering-refresh=' + options.gaID + ']');
				$refreshButton.on('click', function() {
					$gathering.ccmgathering('getNew');
					return false;
				});
				methods.private.enableEditing($gathering, options);
			}
		});	
    }
  };

  $.fn.ccmgathering = function(method) {

    if ( methods[method] ) {
      return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.ccmgathering' );
    }   

  };

})(window, jQuery);
