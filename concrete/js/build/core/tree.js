/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global ccmi18n, ccmi18n_tree, CCM_DISPATCHER_FILENAME, ConcreteAlert, ConcreteEvent, ConcreteMenu */

;(function(global, $) {
	'use strict';

	function ConcreteTree($element, options) {
		var my = this;
		options = options || {};
		options = $.extend({
			readOnly: false,
			chooseNodeInForm: false,
			onSelect: false,
			treeID: false,
			onClick: false,
			allowFolderSelection: true,
			selectNodesByKey: [],
			removeNodesByKey: [],
			removeNodesByCallback: false,
			ajaxData: {} // additional to be sent up
		}, options);
		my.options = options;
		my.$element = $element;
		my.setupTree();
		if (!options.chooseNodeInForm && !options.onClick) {
			ConcreteTree.setupTreeEvents(my);
		}
		return my.$element;
	}


	ConcreteTree.prototype = {

		dragRequest: function(sourceNode, node, hitMode, onSuccess) {
			var treeNodeParentID = node.parent.data.treeNodeID;
			if (hitMode == 'over') {
				treeNodeParentID = node.data.treeNodeID;
			}
			jQuery.fn.dialog.showLoader();
			var params = [{'name': 'sourceTreeNodeID', 'value': sourceNode.data.treeNodeID}, {'name': 'treeNodeParentID', 'value': treeNodeParentID}];

			$.concreteAjax({
				data: params,
				url: CCM_DISPATCHER_FILENAME + '/ccm/system/tree/node/drag_request',
				success: function (r) {
					if (onSuccess) {
						onSuccess();
					}
				}
			});
		},

		setupTree: function() {
			var my = this,
				options = my.options,
				classNames = {},
				checkbox = false,
				ajaxData = {};

			if (my.options.ajaxData != false) {
				ajaxData = my.options.ajaxData;
			}

			if (!options.treeNodeParentID) {
				ajaxData.treeID = options.treeID;
			} else {
				ajaxData.treeNodeParentID = options.treeNodeParentID;
			}

			if (options.allowFolderSelection) {
				ajaxData.allowFolderSelection = 1;
			}

			var persist = true;

			if (options.chooseNodeInForm) {
				checkbox = true;
				persist = false;
				classNames = {
					'checkbox': 'fancytree-radio'
				};
				if (options.selectNodesByKey.length) {
					ajaxData.treeNodeSelectedIDs = options.selectNodesByKey;
				}
			}

			if (options.chooseNodeInForm === 'multiple') {
				checkbox = true;
				persist = false;
				classNames = {
					'checkbox': 'fancytree-checkbox'
				};
				if (options.selectNodesByKey.length) {
					ajaxData.treeNodeSelectedIDs = options.selectNodesByKey;
				}
			}

			var selectMode = 1;
			if(options.selectMode) {
				selectMode = options.selectMode;
			}
			var minExpandLevel = 2;
			if (options.minExpandLevel) {
				minExpandLevel = options.minExpandLevel;
			}

			var ajaxURL;
			if (!options.treeNodeParentID) {
				ajaxURL = CCM_DISPATCHER_FILENAME + '/ccm/system/tree/load';
			} else {
				ajaxURL = CCM_DISPATCHER_FILENAME + '/ccm/system/tree/node/load_starting';
			}

			$(my.$element).fancytree({
				tabindex: null,
				titlesTabbable: false,
				extensions: ["glyph", "dnd"],
				glyph: {
					map: {
						doc: "fa fa-file-o",
						docOpen: "fa fa-file-o",
						checkbox: "fa fa-square-o",
						checkboxSelected: "fa fa-check-square-o",
						checkboxUnknown: "fa fa-share-square",
						dragHelper: "fa fa-share",
						dropMarker: "fa fa-angle-right",
						error: "fa fa-warning",
						expanderClosed: "fa fa-plus-square-o",
						expanderLazy: "fa fa-plus-square-o",  // glyphicon-expand
						expanderOpen: "fa fa-minus-square-o",  // glyphicon-collapse-down
						loading: "fa fa-spin fa-refresh"
					}
				},
				source: {
					url: ajaxURL,
					type: 'post',
					data: ajaxData
				},
				lazyLoad: function(event, data) {
					data.result = my.getLoadNodePromise(data.node);
				},
				select: function(select, data) {
					if (options.chooseNodeInForm) {
						var keys = $.map(data.tree.getSelectedNodes(), function(node) {
							return node.key;
						});
						options.onSelect(keys);
					}
				},

				selectMode: selectMode,
				checkbox: checkbox,
				minExpandLevel:  minExpandLevel,
				clickFolderMode: 1,
				init: function() {

					var $tree = my.$element;

					if (options.removeNodesByKey.length) {
						for (var i = 0; i < options.removeNodesByKey.length; i++) {
							var nodeID = options.removeNodesByKey[i];
							var node = $tree.fancytree('getTree').getNodeByKey(nodeID);
							if (node) {
								node.remove();
							}
						}
					}

					if (options.readOnly) {
						$tree.fancytree('disable');
					}

					var selectedNodes;
					if (options.chooseNodeInForm) {
						selectedNodes = $tree.fancytree('getTree');
						selectedNodes = selectedNodes.getSelectedNodes();
						if (selectedNodes.length) {
							var keys = $.map(selectedNodes, function(node) {
								return node.key;
							});
							options.onSelect(keys);
						}
					}
					if (selectedNodes) {
						$.map(selectedNodes, function(node){
							node.makeVisible();
						});
					}
				},

				click: function(e, data) {

					if (data.targetType == 'expander') {
						return true;
					}

					if (data.targetType == 'icon') {
						return false;
					}

					if (options.onClick) {
						return options.onClick(data.node, e);
					}

					if (options.chooseNodeInForm && data.targetType != 'checkbox') {
						return false;
					}

					if (!data.targetType) {
						return false;
					}

					if (!options.chooseNodeInForm && e.originalEvent.target && $(e.originalEvent.target).hasClass("fancytree-title")) {
						var $menu = data.node.data.treeNodeMenu;
						if ($menu) {
							var menu = new ConcreteMenu($(data.node.span), {
								menu: $menu,
								handle: 'none'
							});
							menu.show(e);
						}
					}

					return true;
				},

				dnd: {
					preventRecursiveMoves: true, // Prevent dropping nodes on own descendants,
					focusOnClick: true,
					preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
					dragStart: function(sourceNode, data) {
						if (!options.chooseNodeInForm) {
							return true;
						} else {
							return false;
						}
					},
					dragStop: function(sourceNode, data) {
						return true;
					},

					dragEnter: function(targetNode, data) {

						var sourceNode = data.otherNode, hitMode = data.hitMode;

						if ((!targetNode.parent.data.treeNodeID) && (targetNode.data.treeNodeID !== '1')) { // Home page has no parents, but we still want to be able to hit it.
							return false;
						}

						if((hitMode != 'over') && (targetNode.data.treeNodeID == 1)) {  // Home gets no siblings
							return false;
						}

						if (sourceNode.data.treeNodeID == targetNode.data.treeNodeID) {
							return false; // can't drag node onto itself.
						}

						if (!targetNode.data.treeNodeID && hitMode == 'after') {
							return false;
						}

						// Prevent dropping a parent below it's own child
						if(targetNode.isDescendantOf(sourceNode)){
							return false;
						}
						return true;
					},
					dragDrop: function(targetNode, data) {
						my.dragRequest(data.otherNode, targetNode, data.hitMode, function() {
							data.otherNode.moveTo(targetNode, data.hitMode);
                            var treeNodeParentID = data.otherNode.parent.data.treeNodeID;
                            if (data.hitMode == 'over') {
                                treeNodeParentID = targetNode.data.treeNodeID;
                            }
                            var params = [{'name': 'sourceTreeNodeID', 'value': data.otherNode.data.treeNodeID}, {'name': 'treeNodeParentID', 'value': treeNodeParentID}];
                            var childNodes = targetNode.parent.getChildren();
                            if (childNodes) {
                                for (var i = 0; i < childNodes.length; i++) {
                                    var childNode = childNodes[i];
                                    params.push({'name': 'treeNodeID[]', 'value': childNode.data.treeNodeID});
                                }
                            }
                            $.concreteAjax({
                                data: params,
                                url: CCM_DISPATCHER_FILENAME + '/ccm/system/tree/node/update_order'
                            });
						});
					}
				}
			});
		},

		getLoadNodePromise: function(node) {
			var my = this,
				ajaxData = my.options.ajaxData != false ? my.options.ajaxData : {};

			ajaxData.treeNodeParentID = node.data.treeNodeID;

			return $.when($.getJSON(CCM_DISPATCHER_FILENAME + '/ccm/system/tree/node/load',
				ajaxData
			));
		},

		reloadNode: function(node, onComplete) {
			this.getLoadNodePromise(node).done(function(data) {
				node.removeChildren();
				node.addChildren(data);
				node.setExpanded(true, {noAnimation: true});
				if (onComplete) {
					onComplete();
				}
			});
		},

		cloneNode: function(treeNodeID) {
			var my = this;
			var $tree = $('[data-tree=' + my.options.treeID + ']');
			$.ajax({
				'dataType': 'json',
				'type': 'post',
				'data': {
					'treeNodeID': treeNodeID
				},
				url: CCM_DISPATCHER_FILENAME + '/ccm/system/tree/node/duplicate',
				success: function(r) {
					if (r.error == true) {
						ConcreteAlert.dialog(ccmi18n.error, r.errors.join("<br>"));
					} else {
						jQuery.fn.dialog.closeTop();
						var node = $tree.fancytree('getTree').getNodeByKey(r.treeNodeParentID);
						jQuery.fn.dialog.showLoader();
						my.reloadNode(node, function() {
							jQuery.fn.dialog.hideLoader();
						});
					}
				},
				error: function(r) {
					ConcreteAlert.dialog(ccmi18n.error, '<div class="alert alert-danger">' + r.responseText + '</div>');
				},
				complete: function() {
					jQuery.fn.dialog.hideLoader();
				}
			});
			return false;
		}

	};

	ConcreteTree.setupTreeEvents = function(my) {
        ConcreteEvent.unsubscribe('ConcreteMenuShow');
		ConcreteEvent.subscribe('ConcreteMenuShow', function(e, data) {
			var $menu = data.menuElement;
			$menu.find('a[data-tree-action]').on('click.concreteMenu', function(e) {
				e.preventDefault();
				var url = $(this).attr('data-tree-action-url'),
					action = $(this).attr('data-tree-action'),
					title = $(this).attr('dialog-title'),
					width = $(this).attr('dialog-width'),
					height = $(this).attr('dialog-height');

				switch(action) {
					case 'clone-node':
						my.cloneNode($(this).attr('data-tree-node-id'));
						break;
					default:
						if (!title) {
							switch(action) {
								case 'add-node':
									title = ccmi18n_tree.add;
									break;
								case 'edit-node':
									title = ccmi18n_tree.edit;
									break;
								case 'delete-node':
									title = ccmi18n_tree.delete;
									break;
							}
						}
						if (!width) {
							width = 550;
						}

						if (!height) {
							height = 'auto';
						}

						jQuery.fn.dialog.open({
							title: title,
							href: url,
							width: width,
							modal: true,
							height: height
						});
						break;
				}
			});
		});

		ConcreteEvent.subscribe('ConcreteTreeAddTreeNode.concreteTree', function(e, r) {
			var $tree = $('[data-tree=' + my.options.treeID + ']'),
				nodes = r.node,
				node;
			if (nodes.length) {
				for (var i = 0; i < nodes.length; i++) {
					node = $tree.fancytree('getTree').getNodeByKey(nodes[i].treeNodeParentID);
					node.addChildren(nodes);
				}
			} else {
				node = $tree.fancytree('getTree').getNodeByKey(nodes.treeNodeParentID);
				node.addChildren(nodes);
			}
		});
		ConcreteEvent.subscribe('ConcreteTreeUpdateTreeNode.concreteTree', function(e, r) {
			var $tree = $('[data-tree=' + my.options.treeID + ']'),
				node = $tree.fancytree('getTree').getNodeByKey(r.node.key);
			node.fromDict(r.node);
			node.render();
		});
		ConcreteEvent.subscribe('ConcreteTreeDeleteTreeNode.concreteTree', function(e, r) {
			var $tree = $('[data-tree=' + my.options.treeID + ']'),
				node = $tree.fancytree('getTree').getNodeByKey(r.node.treeNodeID);
			node.remove();
		});
	};

	// jQuery Plugin
	$.fn.concreteTree = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteTree($(this), options);
		});
	};

	global.ConcreteTree = ConcreteTree;

})(this, jQuery);
