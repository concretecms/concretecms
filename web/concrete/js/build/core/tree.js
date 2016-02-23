
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
			removeNodesByKey: []
		}, options);
		my.options = options;
		my.$element = $element;
		my._menuTemplate = _.template(my.getMenu());
		my.setupTree();
		my.setupTreeEvents();
		return my.$element;
	}

	ConcreteTree.prototype = {

        setupTreeEvents: function() {
			var my = this;
			ConcreteEvent.subscribe('ConcreteMenuShow', function(e, data) {
				var treeID = data.menu.$menu.attr('data-tree-menu');
				if (treeID && treeID == my.options.treeID) {
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
									modal: false,
									height: height
								});
								break;
						}
					});
				}
			});

			ConcreteEvent.subscribe('ConcreteTreeAddTreeNode', function(e, r) {
				var $tree = $('[data-topic-tree=' + my.options.treeID + ']'),
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
			ConcreteEvent.subscribe('ConcreteTreeUpdateTreeNode', function(e, r) {
				var $tree = $('[data-topic-tree=' + my.options.treeID + ']'),
					node = $tree.dynatree('getTree').getNodeByKey(r.node.key);
				node.data = r.node;
				node.render();
			});
			ConcreteEvent.subscribe('ConcreteTreeDeleteTreeNode', function(e, r) {
				console.log(r);
				var $tree = $('[data-topic-tree=' + my.options.treeID + ']'),
					node = $tree.dynatree('getTree').getNodeByKey(r.node.treeNodeID);
				node.remove();
			});
		},

		getMenu: function() {
			return '<div data-tree-menu="<%=data.treeID%>" class="ccm-topic-menu ccm-popover-page-menu popover fade popover fade">' +
				'<div class="arrow"></div><div class="popover-inner"><ul class="dropdown-menu">' +
				'<% if (data.canAddCategoryTreeNode) { %>' +
					'<li><a href="#" data-tree-action="add-node" dialog-title="' + ccmi18n_topics.addCategory + '" data-tree-action-url="' +
                    CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/tree/node/add/category?treeNodeID=<%=data.key%>">' +
                    ccmi18n_topics.addCategory + '<\/a><\/li>' +
				'<% } %>' +

				'<% if (data.canAddTopicTreeNode) { %>' +
					'<li><a href="#" data-tree-action="add-node" dialog-title="' + ccmi18n_topics.addTopic + '" data-tree-action-url="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/tree/node/add/topic?treeNodeID=<%=data.key%>">' + ccmi18n_topics.addTopic + '<\/a><\/li>' +
				'<% } %>' +
				'<% if (data.canEditTreeNode && data.treeNodeTypeHandle == "category") { %>' +
					'<li><a href="#" data-tree-action="edit-node" dialog-title="' + ccmi18n_topics.editCategory + '" data-tree-action-url="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/tree/node/edit/category?treeNodeID=<%=data.key%>">' + ccmi18n_topics.editCategory + '<\/a><\/li>' +
				'<% } %>' +
				'<% if (data.canDuplicateTreeNode && data.treeNodeTypeHandle == "category") { %>' +
					'<li><a href="#" data-tree-action="clone-node" data-tree-node-id="<%=data.key%>">' + ccmi18n_topics.cloneCategory + '<\/a><\/li>' +
				'<% } %>' +
				'<% if (data.canEditTreeNode && data.treeNodeTypeHandle == "topic") { %>' +
					'<li><a href="#" data-tree-action="edit-node" dialog-title="' + ccmi18n_topics.editTopic + '" data-tree-action-url="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/tree/node/edit/topic?treeNodeID=<%=data.key%>">' + ccmi18n_topics.editTopic + '<\/a><\/li>' +
				'<% } %>' +
				'<% if (data.canDuplicateTreeNode && data.treeNodeTypeHandle == "topic") { %>' +
					'<li><a href="#" data-tree-action="clone-node" data-tree-node-id="<%=data.key%>">' + ccmi18n_topics.cloneTopic + '<\/a><\/li>' +
				'<% } %>' +
				'<% if (data.canEditTreeNodePermissions) { %>' +
				'<li><a href="#" data-tree-action="edit-node" dialog-title="' + ccmi18n_topics.editPermissions + '" data-tree-action-url="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/tree/node/permissions?treeNodeID=<%=data.key%>">' + ccmi18n_topics.editPermissions + '<\/a><\/li>' +
				'<% } %>' +
				'<% if (data.treeNodeParentID > 0 && data.treeNodeTypeHandle == "category" && data.canDeleteTreeNode) { %>' +
				'<li><a href="#" data-tree-action="delete-node" dialog-title="' + ccmi18n_topics.deleteCategory + '" data-tree-action-url="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/tree/node/delete?treeNodeID=<%=data.key%>">' + ccmi18n_topics.deleteCategory + '<\/a><\/li>' +
				'<% } %>' +

			'<% if (data.treeNodeParentID > 0 && data.treeNodeTypeHandle == "topic" && data.canDeleteTreeNode) { %>' +
			'<li><a href="#" data-tree-action="delete-node" dialog-title="' + ccmi18n_topics.deleteTopic + '" data-tree-action-url="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/tree/node/delete?treeNodeID=<%=data.key%>">' + ccmi18n_topics.deleteTopic + '<\/a><\/li>' +
			'<% } %>' +
			'</ul></div></div>';

		},

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

			ajaxData.treeID = options.treeID;

			if (options.chooseNodeInForm) {
				checkbox = true;
				switch(options.chooseNodeInForm) {
					case 'single':
						classNames = {'checkbox': 'dynatree-radio'};
						break;
					case 'multiple':
						classNames = {'checkbox': 'dynatree-checkbox'};
						break;

				}
				if (options.selectNodesByKey.length) {
					ajaxData.treeNodeSelectedIDs = options.selectNodesByKey;
				}
			}

			$(my.$element).dynatree({
				autoFocus: false,
				cookieId: 'ConcreteGroups',
				cookie: {
					path: CCM_REL + '/'
				},
				initAjax: {
					url: CCM_DISPATCHER_FILENAME + '/ccm/system/tree/load',
					type: 'post',
					data: ajaxData
				},
				onLazyRead: function(node) {
					my.reloadNode(node);
				},
				onSelect: options.onSelect,
				selectMode: options.chooseNodeInForm === 'multiple' ? 3 : 1, // allow multi-select for checkboxes
				checkbox: checkbox,
				classNames: classNames,
				minExpandLevel: options.minimumExpandLevel,
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

					if (!node.getEventTargetType(e)) {
						return false;
					}
					if (!options.chooseNodeInForm && node.getEventTargetType(e) == 'title') {
						if (options.onClick) {
							if (!node.data.gID) {
								return false;
							}
							options.onClick(node);
						} else {
							var $menu = my._menuTemplate({options: my.options, data: node.data});
							if ($menu) {
								var menu = new ConcreteMenu($(node.span), {
									menu: $menu,
									handle: 'none'
								});
								menu.show(e);
							}
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
				params = {
					url: CCM_DISPATCHER_FILENAME + '/ccm/system/tree/node/load',
					data: {
						treeNodeParentID: node.data.key
					},
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
			var $tree = $('[data-topic-tree=' + my.options.treeID + ']');
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

	// jQuery Plugin
	$.fn.concreteTree = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteTree($(this), options);
		});
	};

	global.ConcreteTree = ConcreteTree;

}(this, $, _);