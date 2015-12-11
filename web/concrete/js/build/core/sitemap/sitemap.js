/**
 * Base search class for AJAX forms in the UI
 */

!function(global, $, _) {
	'use strict';

	function ConcreteSitemap($element, options) {
		var my = this;
		options = options || {};
		options = $.extend({
			displayNodePagination: false,
			cParentID: 0,
			cookieId: 'ConcreteSitemap',
			includeSystemPages: false,
            displaySingleLevel: false,
			minExpandLevel: false,
			dataSource: CCM_TOOLS_PATH + '/dashboard/sitemap_data',
			ajaxData: {},
			selectMode: false, // 1 - single, 2 = multi - has NOTHING to do with clicks. If you enable select mode you CANNOT use a click handler.
			onClickNode: false, // This handles clicking on the title.
			onSelectNode: false, // this handles when a radio or checkbox in the tree is checked
			onPostInit: false
		}, options);
		my.options = options;
		my.$element = $element;
		my.setupTree();
		my.setupTreeEvents();

        Concrete.event.publish('ConcreteSitemap', this);

		return my.$element;
	}

	ConcreteSitemap.prototype = {

		getTree: function() {
			var my = this;
			return my.$element.dynatree('getTree');
		},

		setupTree: function() {
			var minExpandLevel,
				my = this,
				doPersist = true;

			var dynatreeSelectMode = 1,
				checkbox = false,
				classNames = false;

			if (my.options.selectMode == 'single') {
				checkbox = true;
				classNames = {checkbox: "dynatree-radio"};
			} else if (my.options.selectMode == 'multiple') {
				dynatreeSelectMode = 2;
				checkbox = true;
			}

			if (checkbox) {
				doPersist = false;
			}

			if (my.options.minExpandLevel !== false) {
				minExpandLevel = my.options.minExpandLevel;
			} else {
				if (my.options.displaySingleLevel) {
					if (my.options.cParentID == 1) {
						minExpandLevel = 2;
					} else {
						minExpandLevel = 3;
					}
					doPersist = false;
				} else {
					if (my.options.selectMode) {
						minExpandLevel = 2;
					} else {
						minExpandLevel = 1;
					}
				}
			}

			var ajaxData = $.extend({
				'displayNodePagination': my.options.displayNodePagination ? 1 : 0,
				'cParentID': my.options.cParentID,
				'displaySingleLevel': my.options.displaySingleLevel ? 1 : 0,
				'includeSystemPages': my.options.includeSystemPages ? 1 : 0
			}, my.options.ajaxData);

    		$(my.$element).addClass('ccm-tree-sitemap');
    		$(my.$element).dynatree({
                onQueryExpand: function () {
                    (my.options.onQueryExpand || $.noop).apply(this, arguments);
                },
				autoFocus: false,
				classNames: classNames,
				cookieId: my.options.cookieId,
				cookie: {
					path: CCM_REL + '/'
				},
				persist: doPersist,
				initAjax: {
					url: my.options.dataSource,
					data: ajaxData
				},
				onPostInit: function() {
					if (my.options.onPostInit) {
						my.options.onPostInit.call();
					}
					if (my.options.displayNodePagination) {
						my.setupNodePagination(my.$element, my.options.cParentID);
					}
				},
                onRender: function(node, span) {
					if (my.options.selectMode != false) {
						$(span).find('.fa').remove();
					}
                    my.$element.children('.ccm-pagination-bound').remove();
                },
				selectMode: dynatreeSelectMode,
				checkbox: checkbox,
				minExpandLevel:  minExpandLevel,
				clickFolderMode: 2,
				onLazyRead: function(node) {
					if (my.options.displaySingleLevel) {
						my.displaySingleLevel(node);
					} else {
						my.reloadNode(node);
					}
				},
				onExpand: function(expand, node) {
					if (expand && my.options.displaySingleLevel) {
						my.displaySingleLevel(node);
					}
				},
				onClick: function(node, e) {
					if (node.getEventTargetType(e) == "title" && node.data.cID) {

						// I have a select mode, so clicking on the title does nothing.
						if (my.options.selectMode) {
							return false;
						}

						// I have a special on click handler, so we run that. It CAN return
						// false to disable the on click, but it probably won't.
						if (my.options.onClickNode) {
							return my.options.onClickNode.call(my, node);
						}

						// Standard sitemap dashboard mode.
						var menu = new ConcretePageMenu($(node.span).find('>a'), {
							menuOptions: my.options,
							data: node.data,
							sitemap: my,
							onHide: function(menu) {
								menu.$launcher.each(function() {
									$(this).unbind('mousemove.concreteMenu');
								});
							}
						});
						menu.show(e);

					} else if (node.data.href) {
						window.location.href = node.data.href;
					} else if (node.data.displaySingleLevel) {
						my.displaySingleLevel(node);
					}
				},
				onSelect: function(flag, node) {
					if (my.options.onSelectNode) {
						my.options.onSelectNode.call(my, node, flag);
					}
				},

				fx: {height: 'toggle', duration: 200},
				dnd: {
					onDragStart: function(node) {
						if (my.options.selectMode) {
							return false;
						}
						if (node.data.cID) {
							return true;
						}
						return false;
					},
					onDragStop: function(node) {

					},
					autoExpandMS: 1000,
					preventVoidMoves: false,
					onDragEnter: function(node, sourceNode) {
						return true;
					},
					onDragOver: function(node, sourceNode, hitMode) {
						if ((!node.parent.data.cID) && (node.data.cID !== '1')) { // Home page has no parents, but we still want to be able to hit it.
							return false;
						}

                        if((hitMode != 'over') && (node.data.cID == 1)) {  // Home gets no siblings
                            return false;
                        }

                        if (sourceNode.data.cID == node.data.cID) {
                            return false; // can't drag node onto itself.
                        }

						if (!node.data.cID && hitMode == 'after') {
							return false;
						}

				        // Prevent dropping a parent below it's own child
				        if(node.isDescendantOf(sourceNode)){
				          return false;
				        }
				        return true;

					},
					onDrop: function(node, sourceNode, hitMode, ui, draggable) {
						if (node.parent.data.cID == sourceNode.parent.data.cID && hitMode != 'over') {
							// we are reordering
				        	sourceNode.move(node, hitMode);
							my.rescanDisplayOrder(sourceNode.parent);
						} else {
							// we are dragging either onto a node or into another part of the site
							my.selectMoveCopyTarget(sourceNode, node, hitMode);
						}
					}
				}
			});
		},

		/**
		 * These are events that are useful when the sitemap is in the Dashboard, but
		 * they should NOT be listened to when the sitemap is in select Mode.
		 */
		setupTreeEvents: function() {
			var my = this;
			if (my.options.selectMode || my.options.onClickNode) {
				return false;
			}
            ConcreteEvent.unsubscribe('SitemapDeleteRequestComplete.sitemap');
			ConcreteEvent.subscribe('SitemapDeleteRequestComplete.sitemap', function(e) {
	 			var node = my.$element.dynatree('getActiveNode');
				var parent = node.parent;
				my.reloadNode(parent);
			});
            ConcreteEvent.unsubscribe('SitemapAddPageRequestComplete.sitemap');
            ConcreteEvent.subscribe('SitemapAddPageRequestComplete.sitemap', function(e, data) {
                var node = my.getTree().getNodeByKey(data.cParentID);
                if (node) {
                    my.reloadNode(node);
                }
                jQuery.fn.dialog.closeAll();
            });
            ConcreteEvent.subscribe('SitemapUpdatePageRequestComplete.sitemap', function(e, data) {
				try {
					var node = my.getTree().getNodeByKey(data.cID);
					var parent = node.parent;
					if (parent) {
						my.reloadNode(parent);
					}
				} catch(e) {}
            });
		},

    	rescanDisplayOrder: function(node) {
			var childNodes = node.getChildren(),
				params = [],
				i;

			node.setLazyNodeStatus(DTNodeStatus_Loading);
			for (i = 0; i < childNodes.length; i++) {
				var childNode = childNodes[i];
				params.push({'name': 'cID[]', 'value': childNode.data.cID});
			}
			$.concreteAjax({
				dataType: 'json',
				type: 'POST',
				data: params,
				url: CCM_TOOLS_PATH + '/dashboard/sitemap_update',
				success: function(r) {
					node.setLazyNodeStatus(DTNodeStatus_Ok);
					ConcreteAlert.notify({
					'message': r.message
					});

				}
			});
    	},


    	selectMoveCopyTarget: function(node, destNode, dragMode) {
    		var my = this;
			var dialog_title = ccmi18n_sitemap.moveCopyPage;
			if (!dragMode) {
				var dragMode = '';
			}
			var dialog_url = CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request?origCID=' + node.data.cID + '&destCID=' + destNode.data.cID + '&dragMode=' + dragMode;
			var dialog_height = 350;
			var dialog_width = 350;

			$.fn.dialog.open({
				title: dialog_title,
				href: dialog_url,
				width: dialog_width,
				modal: false,
				height: dialog_height
			});

            ConcreteEvent.unsubscribe('SitemapDragRequestComplete.sitemap');
			ConcreteEvent.subscribe('SitemapDragRequestComplete.sitemap', function(e, data) {
				var reloadNode = destNode.parent;
				if (dragMode == 'over') {
					reloadNode = destNode;
				}
                if (data.task == 'MOVE') {
                    node.remove();
                }
				reloadNode.removeChildren();

				my.reloadNode(reloadNode, function() {
					if (!destNode.bExpanded) {
						destNode.expand(true);
					}
				});
			});

    	},


    	setupNodePagination: function($tree, nodeKey) {
    		//var tree = $tree.dynatree('getTree');
    		var pg = $tree.find('div.ccm-pagination-wrapper');
    		$tree.children('.ccm-pagination-bound').remove();
    		if (pg.length) {
    			pg.find('a').unbind('click').on('click', function() {
    				// load under node
    				var href = $(this).attr('href');
    				$tree.dynatree('option', 'initAjax', {
    					url: href
    				});
    				$tree.dynatree('getTree').reload();
    				return false;
    			});
                var node = $.ui.dynatree.getNode(pg);
                if (node && typeof node.remove === 'function') {
                    node.remove();
                }
	    		pg.addClass('ccm-pagination-bound').appendTo($tree);

				$tree.dynatree('option', 'onActivate', function(node) {
					if ($(node.span).hasClass('ccm-sitemap-explore-paging')) {
						node.deactivate();
					}
				});
	    	}
    	},

    	displaySingleLevel: function(node) {
    		var my = this,
    			options = my.options,
    			minExpandLevel = (node.data.cID == 1) ? 2 : 3;

            (my.options.onDisplaySingleLevel || $.noop).call(this, node);

    		var root = my.$element.dynatree('getRoot');
			$(node.li).closest('[data-sitemap=container]').dynatree('option', 'minExpandLevel', minExpandLevel);
			root.removeChildren();
			var ajaxData = $.extend({
				'displayNodePagination': options.displayNodePagination ? 1 : 0,
				'cParentID': node.data.cID,
				'displaySingleLevel': true,
				'includeSystemPages': options.includeSystemPages ? 1 : 0
			}, options.ajaxData);
			root.appendAjax({
				url: options.dataSource,
				data: ajaxData,
				success: function() {
					my.setupNodePagination(root.tree.$tree, node.data.key);
				}
			});

    	},

    	reloadNode: function(node, onComplete) {
    		var my = this,
    			options = my.options,
				ajaxData = $.extend({
					cParentID: node.data.cID,
					'includeSystemPages': options.includeSystemPages ? 1 : 0,
					'displayNodePagination': options.displayNodePagination ? 1 : 0
				}, options.ajaxData),

    			params = {
					url: options.dataSource,
					data: ajaxData,
					success: function() {
						if (onComplete) {
							onComplete();
						}
					}
				};

			node.appendAjax(params);
    	}

	}

	/**
	 * Static methods
	 */

    ConcreteSitemap.exitEditMode = function(cID) {
		$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_check_in?cID=" + cID  + "&ccm_token=" + CCM_SECURITY_TOKEN);
	}

	ConcreteSitemap.refreshCopyOperations = function() {
		ccm_triggerProgressiveOperation(CCM_TOOLS_PATH + '/dashboard/sitemap_copy_all', [],	ccmi18n_sitemap.copyProgressTitle, function() {
			$('.ui-dialog-content').dialog('close');
			window.location.reload();
		});
	}

	ConcreteSitemap.submitDragRequest = function() {

		var origCID = $('#origCID').val();
		var destParentID = $('#destParentID').val();
		var destCID = $('#destCID').val();
		var dragMode = $('#dragMode').val();
		var destSibling = $('#destSibling').val();
		var ctask = $("input[name=ctask]:checked").val();
		var copyAll = $("input[name=copyAll]:checked").val();
		var saveOldPagePath = $("input[name=saveOldPagePath]:checked").val();
		var params = {

			'origCID': origCID,
			'destCID': destCID,
			'ctask': ctask,
			'ccm_token': CCM_SECURITY_TOKEN,
			'copyAll': copyAll,
			'destSibling': destSibling,
			'dragMode': dragMode,
			'saveOldPagePath': saveOldPagePath
		};


		if (copyAll == 1) {

			var dialogTitle = ccmi18n_sitemap.copyProgressTitle;
			ccm_triggerProgressiveOperation(
				CCM_TOOLS_PATH + '/dashboard/sitemap_copy_all',
				[{'name': 'origCID', 'value': origCID}, {'name': 'destCID', 'value': destCID}],
				dialogTitle, function() {
					$('.ui-dialog-content').dialog('close');
					ConcreteEvent.publish('SitemapDragRequestComplete', {'task': ctask});
				}
			);

		} else {

			jQuery.fn.dialog.showLoader();

			$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request', params, function(resp) {
				// parse response
				ccm_parseJSON(resp, function() {
					jQuery.fn.dialog.closeAll();
					jQuery.fn.dialog.hideLoader();
					ConcreteAlert.notify({
					'message': resp.message
					});

					ConcreteEvent.publish('SitemapDragRequestComplete', {'task': ctask});
					jQuery.fn.dialog.closeTop();
					jQuery.fn.dialog.closeTop();
				});
			});
		}
	}

	// jQuery Plugin
	$.fn.concreteSitemap = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteSitemap($(this), options);
		});
	}

	global.ConcreteSitemap = ConcreteSitemap;

}(this, $, _);
