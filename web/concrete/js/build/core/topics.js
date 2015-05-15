(function($, window) {

  var methods = {

    private:  {

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

		getMenu: function(data, options) {
			var menu = '<div class="ccm-topic-menu ccm-popover-page-menu popover fade popover fade"><div class="arrow"></div><div class="popover-inner">';
			menu += '<ul class="dropdown-menu">';

			if (data.canAddTopicCategoryTreeNode) {
				menu += '<li><a class="dialog-launch" dialog-width="550" dialog-on-open="$(\'[data-topic-form=add-category-node]\').ccmtopicstree(\'initAddNodeForm\', ' + options.treeID + ');" dialog-height="auto" dialog-modal="false" dialog-title="' + ccmi18n_topics.addCategory + '" href="' + CCM_DISPATCHER_FILENAME + '/tools/required/tree/node/add/topic_category?treeNodeParentID=' + data.key + '">' + ccmi18n_topics.addCategory + '<\/a><\/li>';
			}
			if (data.canAddTopicTreeNode) {
				menu += '<li><a class="dialog-launch" dialog-width="550" dialog-on-open="$(\'[data-topic-form=add-topic-node]\').ccmtopicstree(\'initAddNodeForm\', ' + options.treeID + ');" dialog-height="auto" dialog-modal="false" dialog-title="' + ccmi18n_topics.addTopic + '" href="' + CCM_DISPATCHER_FILENAME + '/tools/required/tree/node/add/topic?treeNodeParentID=' + data.key + '">' + ccmi18n_topics.addTopic + '<\/a><\/li>';
			}

			if (data.canEditTreeNode && data.treeNodeTypeHandle == 'topic_category') {
				menu += '<li><a class="dialog-launch" dialog-width="550" dialog-on-open="$(\'[data-topic-form=update-category-node]\').ccmtopicstree(\'initUpdateCategoryNodeForm\', ' + options.treeID + ');" dialog-height="auto" dialog-modal="false" dialog-title="' + ccmi18n_topics.editCategory + '" href="' + CCM_DISPATCHER_FILENAME + '/tools/required/tree/node/edit/topic_category?treeNodeID=' + data.key + '">' + ccmi18n_topics.editCategory + '<\/a><\/li>';
			}
			if (data.canDuplicateTreeNode && data.treeNodeTypeHandle == 'topic_category') {
				menu += '<li><a href="#" onclick="$.fn.ccmtopicstree(\'cloneNode\', \'node\', ' + options.treeID + ',' + data.key + ')">' + ccmi18n_topics.cloneCategory + '<\/a><\/li>';
			}

			if (data.canEditTreeNode && data.treeNodeTypeHandle == 'topic') {
				menu += '<li><a class="dialog-launch" dialog-width="550" dialog-on-open="$(\'[data-topic-form=update-topic-node]\').ccmtopicstree(\'initUpdateTopicNodeForm\', ' + options.treeID + ');" dialog-height="auto" dialog-modal="false" dialog-title="' + ccmi18n_topics.editTopic + '" href="' + CCM_DISPATCHER_FILENAME + '/tools/required/tree/node/edit/topic?treeNodeID=' + data.key + '">' + ccmi18n_topics.editTopic + '<\/a><\/li>';
			}
			if (data.canDuplicateTreeNode && data.treeNodeTypeHandle == 'topic') {
				menu += '<li><a href="#" onclick="$.fn.ccmtopicstree(\'cloneNode\', \'node\', ' + options.treeID + ',' + data.key + ')">' + ccmi18n_topics.cloneTopic + '<\/a><\/li>';
			}

			if (data.canEditTreeNodePermissions) {
				menu += '<li><a class="dialog-launch" dialog-width="480" dialog-height="auto" dialog-modal="true" dialog-title="' + ccmi18n_topics.editPermissions + '" href="' + CCM_TOOLS_PATH + '/tree/node/permissions?treeNodeID=' + data.key + '">' + ccmi18n_topics.editPermissions + '<\/a><\/li>';
			}
			if (data.treeNodeParentID > 0 && data.treeNodeTypeHandle == 'topic_category' && data.canDeleteTreeNode) {
				menu += '<li><a class="dialog-launch" dialog-width="550" dialog-on-open="$(\'[data-topic-form=remove-tree-node]\').ccmtopicstree(\'initRemoveNodeForm\', ' + options.treeID + ');" dialog-height="auto" dialog-modal="false" dialog-title="' + ccmi18n_topics.deleteCategory + '" href="' + CCM_TOOLS_PATH + '/tree/node/remove?treeNodeID=' + data.key + '">' + ccmi18n_topics.deleteCategory + '<\/a><\/li>';
			}
			if (data.treeNodeParentID > 0 && data.treeNodeTypeHandle == 'topic' && data.canDeleteTreeNode) {
				menu += '<li><a class="dialog-launch" dialog-width="550" dialog-on-open="$(\'[data-topic-form=remove-tree-node]\').ccmtopicstree(\'initRemoveNodeForm\', ' + options.treeID + ');" dialog-height="auto" dialog-modal="false" dialog-title="' + ccmi18n_topics.deleteTopic + '" href="' + CCM_TOOLS_PATH + '/tree/node/remove?treeNodeID=' + data.key + '">' + ccmi18n_topics.deleteTopic + '<\/a><\/li>';
			}
			menu += '</ul></div></div>';
			var $menu = $(menu);
			if ($menu.find('li').length == 0) {
				return false;
			}

			return $menu;

		},
    	

    	reloadNode: function(options, node, onComplete) {
    		var obj = this,
                data = options.ajaxData;
            data.treeNodeParentID = node.data.key;

    		var params = {
				url: CCM_TOOLS_PATH + '/tree/node/load',
				data: data,
				success: function() {
					if (onComplete) {
						onComplete();
					}
				}
			};
			
			node.appendAjax(params);

    	},

		setupDialogForm: function($form, onSuccess) {
	    	$form.closest('.ui-dialog').find("button[type=submit]").on('click', function() { $form.trigger('submit'); });
	    	$form.on('submit', function() {
	    		jQuery.fn.dialog.showLoader();
	    		var data = $form.serializeArray();
	    		$.ajax({
	    			'dataType': 'json',
	    			'type': 'post',
	    			'data': data,
	    			'url': $form.attr('action'),
	    			success: function(r) {
	    				if (r.error == true) {
	    					ConcreteAlert.dialog(ccmi18n.error, r.errors.join("<br>"));
	    				} else {
	    					jQuery.fn.dialog.closeTop();
	    					onSuccess(r);
	    				}
	    			},
	    			error: function(r) {
    					ConcreteAlert.dialog(ccmi18n.error, r.responseText);
	    			},
	    			complete: function() {
	    				jQuery.fn.dialog.hideLoader();
	    			}
	    		});
	    		return false;
	    	});
		}
    },

    initAddNodeForm: function(treeID) {
    	methods.private.setupDialogForm($(this), function(r) {
    		var $tree = $('[data-topic-tree=' + treeID + ']');
    		if (r.length) {
    			for (var i = 0; i < r.length; i++) {
		    		var node = $tree.dynatree('getTree').getNodeByKey(r[i].treeNodeParentID);
    				node.addChild(r[i]);
    			}
    		} else {
	    		var node = $tree.dynatree('getTree').getNodeByKey(r.treeNodeParentID);
	    		node.addChild(r);
	    	}
    		node.expand();
    	});
    },

    cloneNode: function(cloneType, treeID, treeNodeID) {
  		var $tree = $('[data-topic-tree=' + treeID + ']');
		$.ajax({
			'dataType': 'json',
			'type': 'post',
			'data': {
				'treeNodeID': treeNodeID
			},
			'url': CCM_TOOLS_PATH + '/tree/node/duplicate/' + cloneType,
			success: function(r) {
				if (r.error == true) {
					ConcreteAlert.dialog(ccmi18n.error, r.errors.join("<br>"));
				} else {
					jQuery.fn.dialog.closeTop();
		    		var node = $tree.dynatree('getTree').getNodeByKey(r.treeNodeParentID);
					node.setLazyNodeStatus(DTNodeStatus_Loading);	
					methods.private.reloadNode($tree.data('options'), node, function() {
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
    },

    initUpdateCategoryNodeForm: function(treeID) {
    	methods.private.setupDialogForm($(this), function(r) {
    		var $tree = $('[data-topic-tree=' + treeID + ']');
    		var node = $tree.dynatree('getTree').getNodeByKey(r.key);
    		node.data = r;
    		node.render();
    	});
    },

    initUpdateTopicNodeForm: function(treeID) {
    	methods.private.setupDialogForm($(this), function(r) {
    		var $tree = $('[data-topic-tree=' + treeID + ']');
    		var node = $tree.dynatree('getTree').getNodeByKey(r.key);
    		node.data = r;
    		node.render();
    	});
    },


    initRemoveNodeForm: function(treeID) {
    	methods.private.setupDialogForm($(this), function(r) {
    		var $tree = $('[data-topic-tree=' + treeID + ']');
    		var node = $tree.dynatree('getTree').getNodeByKey(r.treeNodeID);
    		node.remove();
    	});
    },

	init: function(options) {
		options = $.extend({
			readonly: false,
			chooseNodeInForm: false,
			onSelect: false,
			allowFolderSelection: false,
			selectNodesByKey: []
		}, options);

		var checkbox = false,
			classNames = false;

		/* var ajaxData =  {
			'treeID': options.treeID
		}; */

		if(!options.treeNodeParentID) {
			var ajaxData = { 'treeID': options.treeID };
		} else {
			var ajaxData = { 'treeNodeParentID': options.treeNodeParentID };
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
		if(options.minExpandLevel) {
			minExpandLevel = options.minExpandLevel;
		}

		return this.each(function() {
			if(!options.treeNodeParentID) {
				var loadToolsURL = CCM_TOOLS_PATH + '/tree/load';
			} else {
				var loadToolsURL = CCM_TOOLS_PATH + '/tree/node/load';
			}
			var $obj = $(this);
            options.ajaxData = ajaxData;
			$obj.data('options', options);
			$obj.dynatree({
				autoFocus: false,
				/*cookieId: cookieId,
				cookie: {
					path: CCM_REL + '/'
				},*/

				onSelect: function(select, node) {
					if (options.chooseNodeInForm) {
						options.onSelect(select, node);
					}
				},
				/*
				persist: persist,
				*/
				selectMode: selectMode,
				checkbox: checkbox,
				classNames: classNames,
				minExpandLevel:  minExpandLevel,
				clickFolderMode: 1,
				initAjax: {
					url: loadToolsURL,
					type: 'post',
					data: ajaxData
				},
				onLazyRead: function(node) {
					methods.private.reloadNode(options, node);
				},

				onPostInit: function() {
		    		var $tree = $obj;
					if (options.readonly) {
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
					if(selectedNodes) {
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
					if (!options.chooseNodeInForm && node.getEventTargetType(e) == 'title' && !options.noForm) {
						var $menu = methods.private.getMenu(node.data, options);
						if ($menu) {
							var menu = new ConcreteMenu($(node.span), {
								menu: $menu,
								launcher: 'none'
							});
							menu.show(e);
						}
					}
				},
				dnd: {
					onDragStart: function(node) {
						if(options.noDrag) {
							return false;
						} else {
							return true;
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

						// you can only drag shit into other categories.
						// Restriction lifted. Topics can live inside other topics.
						/*
						var nodeTypeHandle = node.data.treeNodeType;
						if (hitMode === 'over' && jQuery.inArray(node.data.treeNodeTypeHandle, ['topic']) > -1) {
							return false;
						}
						*/

				        // Prevent dropping a parent below its own child
				        if(node.isDescendantOf(sourceNode)){
				          return false;
				        }
				        return true;

					},
					onDrop: function(node, sourceNode, hitMode, ui, draggable) {
						sourceNode.move(node, hitMode);
						methods.private.dragRequest(sourceNode, node, hitMode);
					}
				}				
			});

		});
    }


  };

  $.fn.ccmtopicstree = function(method) {

    if ( methods[method] ) {
      return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.ccmtopicstree' );
    }   

  };
})(jQuery, window);