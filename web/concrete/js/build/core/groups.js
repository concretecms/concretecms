/**
 * Groups tree
 */

!function(global, $, _) {
	'use strict';

	function ConcreteGroupsTree($element, options) {
		var my = this;
		options = options || {};
		options = $.extend({
			treeID: false,
			minimumExpandLevel: 2,
			selectNodeByKey: [],
			enableDragAndDrop: true,
            onSelect: false, // This is when you click a checkbox or radio button
            onClick: false, // this is when you click a group title when not in chooseNodeInForm mode.
			readOnly: false,
			removeNodesByID: [],
			chooseNodeInForm: false // false (no ability to choose a node as a form element), "single" uses radio, "multiple" = checkbox.
		}, options);
		my.options = options;
		my.$element = $element;
		my._menuTemplate = _.template(ConcreteGroupsTree.getMenu());
		my.setupTree();
		my.setupTreeEvents();
		return my.$element;
	}

	ConcreteGroupsTree.prototype = {

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
				url: CCM_TOOLS_PATH + '/tree/node/drag_request',
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
					url: CCM_TOOLS_PATH + '/tree/load',
					type: 'post',
					data: ajaxData
				},
				onLazyRead: function(node) {
					my.reloadNode(node);
				},
                onSelect: options.onSelect,
				selectMode: 1,
				checkbox: checkbox,
				classNames: classNames,
				minExpandLevel: options.minimumExpandLevel,
				clickFolderMode: 1,
				onPostInit: function() {
		    		var $tree = my.$element;

		    		if (options.removeNodesByID.length) {
		    			for (var i = 0; i < options.removeNodesByID.length; i++) {
		    				var nodeID = options.removeNodesByID[i];
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
						if (options.enableDragAndDrop) {
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

		setupTreeEvents: function() {

		},

    	reloadNode: function(node, onComplete) {
    		var my = this,
    			options = my.options,
    			params = {
					url: CCM_TOOLS_PATH + '/tree/node/load',
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
    	}

	};

	/** 
	 * Static methods
	 */

	ConcreteGroupsTree.getMenu = function() {
		return '<div class="ccm-popover-page-menu popover fade" data-search-page-menu="<%=data.cID%>" data-search-menu="<%=data.cID%>">' +
			'<div class="arrow"></div><div class="popover-inner"><ul class="dropdown-menu">' + 
			'<% if (data.canEditTreeNode && data.treeNodeTypeHandle == \'group\' && data.gID) { %>' + 
				'<li><a href="' + CCM_DISPATCHER_FILENAME + '/dashboard/users/groups/-/edit/<%=data.gID%>">' + ccmi18n_groups.editGroup + '</a></li>' + 
			'<% } %>' +
			'<% if (data.canEditTreeNodePermissions) { %>' + 
				'<li><a class="dialog-launch" dialog-width="480" dialog-height="380" dialog-modal="true" dialog-title="Edit Permissions" href="' + CCM_TOOLS_PATH + '/tree/node/permissions?treeNodeID=<%=data.key%>">' + ccmi18n_groups.editPermissions + '</a></li>' + 
			'<% } %>' +
		'</ul></div></div>';
	};

	// jQuery Plugin
	$.fn.concreteGroupsTree = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteGroupsTree($(this), options);
		});
	};

	global.ConcreteGroupsTree = ConcreteGroupsTree;

}(this, $, _);