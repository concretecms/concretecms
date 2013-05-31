/**
 * Sitemap proxy functions to dynatree
 */


(function($, window) {

  var methods = {

    private:  {

    	handleAppendedElements: function(response, $aggregator, options, prepend) {
			var elements = ($('<div />').append(response).find('>div'));
			$.each(elements, function(i, obj) {
				prepend ? $aggregator.prepend(obj) : $aggregator.append(obj);
			});
			if (elements.length > 0) {
				prepend ? $aggregator.packery('prepended', elements) : $aggregator.packery('appended', elements);
				if (options.showTileControls) {
					methods.private.enableEditing($aggregator, options);
				}

				methods.private.enableOverlay($aggregator, options);
				methods.private.enableHover($aggregator, options);
			}
    	},

    	enableOverlay: function($aggregator, options) {
			$aggregator.find('a[data-overlay=aggregator-item]').not('.overlay-bound').each(function() {
				var agiID = $(this).closest('[data-aggregator-item-id]').attr('data-aggregator-item-id');
				$(this).on('click', function() {
					$.magnificPopup.open({
						ajax: {
							settings: {
								'data': {
									'agiID': agiID,
									'token': options.loadToken
								},
								'dataType': 'html',
								'type': 'post'
							}
						},
						items: {
							src: CCM_TOOLS_PATH + '/aggregator/item/detail'
						},
						mainClass: 'ccm-aggregator-overlay-wrapper',	
						type: 'ajax',
						removalDelay: 200
					});
					return false;
				});
			}).addClass('overlay-bound');
    	},

    	enableHover: function($aggregator, options) {

			$aggregator.find('.ccm-aggregator-item').not('.hover-bound').each(function() {
				$(this).on('mouseenter', function() {
					$(this).addClass('ccm-aggregator-item-over')
				}).on('mouseleave', function() {
					$(this).removeClass('ccm-aggregator-item-over')
				});
			}).addClass('hover-bound');
    	},

    	enableEditing: function($aggregator, options) {
			$aggregator.find('a[data-inline-command=options-tile]').not('.aggregator-options-bound').on('click', function(e) {
				var $menu = $('#' + $(this).attr('data-menu'));
				$.fn.ccmmenu.showmenu(e, $menu);
				return false;
			}).addClass('aggregator-options-bound');

			var $itemElements = $($aggregator.packery('getItemElements')).not('.event-bound');
			$itemElements.draggable({
				'handle': 'a[data-inline-command=move-tile]', 
				start: function() {
					$.fn.ccmmenu.disable();
					$('.ccm-area-block-dropzone').addClass('ccm-area-block-dropzone-active');
					$('div[data-aggregator-id]').each(function() {
						var $tagg = $(this);
						if (parseInt($tagg.attr('data-aggregator-id')) != parseInt(options.agID)) {
							$tagg.addClass('ccm-aggregator-item-droppable').droppable({
								accept: '.ccm-aggregator-item',
								tolerance: 'pointer',
								hoverClass: 'ccm-aggregator-item-drop-active',
								drop: function(e, ui) {
									jQuery.fn.dialog.showLoader();
									var $destination = $(this);
									var agID = $destination.attr('data-aggregator-id');
									var data = [
										{'name': 'task', 'value': 'move_to_new_aggregator'},
										{'name': 'agiID', 'value': ui.draggable.attr('data-aggregator-item-id')},
										{'name': 'agID', 'value': $destination.attr('data-aggregator-id')},
										{'name': 'cID', 'value': CCM_CID},
										{'name': 'itemsPerPage', 'value': options.itemsPerPage},
										{'name': 'editToken', 'value': options.editToken}
									];

									var $source = $(ui.draggable).parent();
									$(ui.draggable).remove();

									$.ajax({
										type: 'post',
										url: CCM_TOOLS_PATH + '/aggregator/update',
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
					$.fn.ccmmenu.enable();
					//$('div.ccm-aggregator-item-droppable').droppable('destroy');
					$('.ccm-area-block-dropzone').removeClass('ccm-area-block-dropzone-active');
					$aggregator.packery('layout');
				}
			});

			$aggregator.packery('on', 'dragItemPositioned', function(pkr, item) {
				var data = [
					{'name': 'task', 'value': 'update_display_order'},
					{'name': 'agID', 'value': options.agID},
					{'name': 'editToken', 'value': options.editToken}
				];
				var items = [];
				var elements = pkr.getItemElements();
				for (i = 0; i < elements.length; i++) {
					var $obj = $(elements[i]);
					data.push({'name': 'agiID[]', 'value': $obj.attr('data-aggregator-item-id')});
				}

				$.ajax({
					type: 'post',
					url: CCM_TOOLS_PATH + '/aggregator/update',
					data: data
				});
			});

			$aggregator.packery( 'bindUIDraggableEvents', $itemElements );
			$itemElements.resizable({
				handles: 'se',
				helper: 'ccm-aggregator-resize-helper',
				grid: [options.columnWidth + options.gutter, options.rowHeight + options.gutter],
				stop: function(e, ui) {
					var $tile = ui.element,
					wx = parseInt($tile.css('width')),
					hx = parseInt($tile.css('height')),
					w = Math.floor(wx / options.columnWidth),
					h = Math.floor(hx / options.rowHeight);

					$aggregator.packery('layout');

					$.ajax({
						type: 'post',
						url: CCM_TOOLS_PATH + '/aggregator/update',
						data: {
							'task': 'resize',
							'agID': options.agID,
							'agiID': $tile.attr('data-aggregator-item-id'),
							'agiSlotWidth': w,
							'agiSlotHeight': h,
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
		var options = $.extend({
			reloadItemTile: false
		}, options);
		$.ajax({
			type: 'POST',
			data: {
				task: 'update_item_template',
				agiID: options.agiID,
				agtTypeID: options.agtTypeID,
				agtID: options.agtID,
				token: options.updateToken
			},
			url: CCM_TOOLS_PATH + '/aggregator/item/template',
			success: function(r) {
				jQuery.fn.dialog.hideLoader();
				if (options.reloadItemTile) {
					// load the newly rendered HTML into the old aggregator item.
					$('[data-aggregator-item-id=' + options.agiID + ']').find('div.ccm-aggregator-item-inner-render').html(r);
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
				agiID: options.agiID,
				token: options.deleteToken
			},
			url: CCM_TOOLS_PATH + '/aggregator/item/delete',
			success: function(r) {
				jQuery.fn.dialog.hideLoader();
				var $item = $('[data-aggregator-item-id=' + options.agiID + ']');
				var $aggregator = $item.parent();
				$item.remove();
				$aggregator.packery('layout');
				jQuery.fn.dialog.closeTop();
			}
		});
    },

    getNew: function() {
    	var $aggregator = $(this);
		var options = $(this).data('options');
		jQuery.fn.dialog.showLoader();
		var getNewerThan = $($aggregator.find('.ccm-aggregator-item')[0]).attr('data-aggregator-item-id');
		$.ajax({
			type: 'post',
			url: CCM_TOOLS_PATH + '/aggregator/get_new',
			data: {
				'task': 'get_aggregator_items',
				'newerThan': getNewerThan,
				'agID': options.agID,
				'editToken': options.editToken,
				'showTileControls': options.showTileControls
			},
		
			success: function(r) {
				jQuery.fn.dialog.hideLoader();
				methods.private.handleAppendedElements(r, $aggregator, options, true);
			}
		});
    },

	init: function(options) {

		var options = $.extend({
			totalPages: 0,
			columnWidth: 120,
			itemsPerPage: 24,
			rowHeight: 120,
			showTileControls: false,
			gutter: 1
		}, options);

		return this.each(function() {
			var $aggregator = $(this);
			$(this).data('options', options);
			var $loadButton = $aggregator.parent().find('button[data-aggregator-button=aggregator-load-more-items]');
			if (options.totalPages == 1) {
				$loadButton.hide();
			}
			$aggregator.packery({
				columnWidth: options.columnWidth,
				rowHeight: options.rowHeight,
				gutter: options.gutter
			});
			$aggregator.css('opacity', 1);

			// handle details and lightbox.
			methods.private.enableHover($aggregator, options);

			// handle details and lightbox.
			methods.private.enableOverlay($aggregator, options);

			$loadButton.on('click', function() {
				page = parseInt($aggregator.attr('data-aggregator-current-page')),
					newPage = page + 1;

				$loadButton.prop('disabled', true);

				$.ajax({
					type: 'post',
					url: CCM_TOOLS_PATH + '/aggregator/load_more',
					data: {
						'task': 'get_aggregator_items',
						'agID': options.agID,
						'page': newPage,
						'itemsPerPage': options.itemsPerPage,
						'loadToken': options.loadToken,
						'editToken': options.editToken,
						'showTileControls': options.showTileControls
					},
				
					success: function(r) {
						methods.private.handleAppendedElements(r, $aggregator, options);
						if (newPage == options.totalPages) {
							$loadButton.hide();
						} else {
							$loadButton.prop('disabled', false);
							$aggregator.attr('data-aggregator-current-page', newPage);
						}
					}
				});
			});

			if (options.showTileControls) {
				var $refreshButton = $('[data-aggregator-refresh=' + options.agID + ']');
				$refreshButton.on('click', function() {
					$aggregator.ccmaggregator('getNew');
					return false;
				});
				methods.private.enableEditing($aggregator, options);
			}
		});	
    }
  }

  $.fn.ccmaggregator = function(method) {

    if ( methods[method] ) {
      return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.ccmaggregator' );
    }   

  };
})(jQuery, window);
