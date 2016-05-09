
!function(global, $, _) {
	'use strict';

	function ConcreteTree($element, options) {
		var my = this;
		options = options || {};
		options = $.extend({
			readonly: false,
			chooseNodeInForm: false,
			onSelect: false,
			treeID: false,
			allowFolderSelection: true,
			selectNodesByKey: [],
			removeNodesByKey: [],
			removeNodesByCallback: false,
			ajaxData: {} // additional to be sent up
		}, options);
		my.options = options;
		my.$element = $element;
		my.setupTree();
		ConcreteTree.setupTreeEvents(my);
		return my.$element;
	}


	ConcreteTree.prototype = {

		dragRequest: function(sourceNode, node, hitMode) {
			var treeNodeParentID = node.parent.data.key;
			if (hitMode == 'over') {
				treeNodeParentID = node.data.key;
			}
			jQuery.fn.dialog.showLoader();
			var params = [{'name': 'sourceTreeNodeID', 'value': sourceNode.data.key}, {'name': 'treeNodeParentID', 'value': treeNodeParentID}];
			var childNodes = node.parent.getChildren();
			if (childNodes) {
				for (var i = 0; i < childNodes.length; i++) {
					var childNode = childNodes[i];
					params.push({'name': 'treeNodeID[]', 'value': childNode.data.key});
				}
			}
			$.ajax({
				dataType: 'json',
				type: 'POST',
				data: params,
				url: CCM_DISPATCHER_FILENAME + '/ccm/system/tree/node/drag_request',
				success: function(r) {
					ccm_parseJSON(r, function() {});
					jQuery.fn.dialog.hideLoader();
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
					'checkbox': 'dynatree-radio'
				};
				if (options.selectNodesByKey.length) {
					ajaxData.treeNodeSelectedIDs = options.selectNodesByKey;
				}
			}

			if (options.chooseNodeInForm === 'multiple') {
				checkbox = true;
				persist = false;
				classNames = {
					'checkbox': 'dynatree-checkbox'
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

			if (!options.treeNodeParentID) {
				var ajaxURL = CCM_DISPATCHER_FILENAME + '/ccm/system/tree/load';
			} else {
				var ajaxURL = CCM_DISPATCHER_FILENAME + '/ccm/system/tree/node/load_starting';
			}

			$(my.$element).dynatree({
				autoFocus: false,
				initAjax: {
					url: ajaxURL,
					type: 'post',
					data: ajaxData
				},
				onLazyRead: function(node) {
					my.reloadNode(node);
				},
				onSelect: function(select, node) {
					if (options.chooseNodeInForm) {
						options.onSelect(select, node);
					}
				},
				selectMode: selectMode,
				checkbox: checkbox,
				classNames: classNames,
				minExpandLevel:  minExpandLevel,
				clickFolderMode: 1,
				onPostInit: function() {
					var $tree = my.$element;

					if (options.removeNodesByKey.length) {
						for (var i = 0; i < options.removeNodesByKey.length; i++) {
							var nodeID = options.removeNodesByKey[i];
							var node = this.getNodeByKey(nodeID);
							if (node) {
								node.remove();
							}
						}
					}

					if (options.readOnly) {
						$tree.dynatree('disable');
					}

					if (options.chooseNodeInForm) {
						var selectedNodes = $tree.dynatree('getTree');
						selectedNodes = selectedNodes.getSelectedNodes();
						if (selectedNodes[0]) {
							var node = selectedNodes[0];
							options.onSelect(true, node);
						}
					}
					if (selectedNodes) {
						var selKeys = $.map(selectedNodes, function(node){
							node.makeVisible();
						});
					}
				},
				onClick: function(node, e) {

					if (node.getEventTargetType(e) == 'expander') {
						return true;
					}

					if (options.chooseNodeInForm && node.getEventTargetType(e) != 'checkbox') {
						return false;
					}
					if (!node.getEventTargetType(e)) {
						return false;
					}

					/*
					if (options.chooseNodeInForm) {
						var targetType = node.getEventTargetType(e);
						if (targetType == 'checkbox' || targetType == 'title') {
							if (targetType == 'title') {
								node.select(true);
							}
							return true;
						} else {
							return false;
						}
					}
					*/

					if (!options.chooseNodeInForm && node.getEventTargetType(e) == 'title') {
						var $menu = node.data.treeNodeMenu;
						if ($menu) {
							var menu = new ConcreteMenu($(node.span), {
								menu: $menu,
								handle: 'none'
							});
							menu.show(e);
						}
					}

					return true;
				},
				fx: {height: 'toggle', duration: 200},
				dnd: {
					onDragStart: function(node) {
						if (!options.chooseNodeInForm) {
							return true;
						} else {
							return false;
						}
					},
					onDragStop: function(node) {

					},
					autoExpandMS: 1000,
					preventVoidMoves: true,
					onDragEnter: function(node, sourceNode) {
						return true;
					},
					onDragOver: function(node, sourceNode, hitMode) {
						if ((!node.parent.data.treeNodeID) && (node.data.treeNodeID !== '1')) { // Home page has no parents, but we still want to be able to hit it.
							return false;
						}

						if((hitMode != 'over') && (node.data.treeNodeID == 1)) {  // Home gets no siblings
							return false;
						}

						if (sourceNode.data.treeNodeID == node.data.treeNodeID) {
							return false; // can't drag node onto itself.
						}

						if (!node.data.treeNodeID && hitMode == 'after') {
							return false;
						}

						// Prevent dropping a parent below it's own child
						if(node.isDescendantOf(sourceNode)){
							return false;
						}
						return true;
					},
					onDrop: function(node, sourceNode, hitMode, ui, draggable) {
						sourceNode.move(node, hitMode);
						my.dragRequest(sourceNode, node, hitMode);
					}
				}
			});
		},

		reloadNode: function(node, onComplete) {
			var my = this,
				options = my.options,
				data = my.options.ajaxData != false ? my.options.ajaxData : {};

			data.treeNodeParentID = node.data.key;

				var params = {
					url: CCM_DISPATCHER_FILENAME + '/ccm/system/tree/node/load',
					data: data,
					success: function() {
						if (onComplete) {
							onComplete();
						}
					}
				};

			node.appendAjax(params);
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
						var node = $tree.dynatree('getTree').getNodeByKey(r.treeNodeParentID);
						node.setLazyNodeStatus(DTNodeStatus_Loading);
						my.reloadNode(node, function() {
							node.setLazyNodeStatus(DTNodeStatus_Ok);
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
				nodes = r.node;
			if (nodes.length) {
				for (var i = 0; i < nodes.length; i++) {
					var node = $tree.dynatree('getTree').getNodeByKey(nodes[i].treeNodeParentID);
					node.addChild(nodes[i]);
				}
			} else {
				var node = $tree.dynatree('getTree').getNodeByKey(nodes.treeNodeParentID);
				node.addChild(nodes);
			}
		});
		ConcreteEvent.subscribe('ConcreteTreeUpdateTreeNode.concreteTree', function(e, r) {
			var $tree = $('[data-tree=' + my.options.treeID + ']'),
				node = $tree.dynatree('getTree').getNodeByKey(r.node.key);
			node.data = r.node;
			node.render();
		});
		ConcreteEvent.subscribe('ConcreteTreeDeleteTreeNode.concreteTree', function(e, r) {
			var $tree = $('[data-tree=' + my.options.treeID + ']'),
				node = $tree.dynatree('getTree').getNodeByKey(r.node.treeNodeID);
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

}(this, $, _);