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
				for (i = 0; i < childNodes.length; i++) {
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
			var menu = '<div class="ccm-group-menu ccm-menu popover fade"><div class="arrow"></div><div class="inner"><div class="content" style="padding: 0px">';
			menu += '<ul>';
			if (data.canEditTreeNode && data.treeNodeTypeHandle == 'group' && data.gID) {
				menu += '<li><a href="' + CCM_DISPATCHER_FILENAME + '/dashboard/users/groups/-/edit/' + data.gID + '">Edit Group<\/a><\/li>';
			}

			if (data.canEditTreeNodePermissions) {
				menu += '<li><a class="dialog-launch" dialog-width="480" dialog-height="380" dialog-modal="true" dialog-title="Edit Permissions" href="' + CCM_TOOLS_PATH + '/tree/node/permissions?treeNodeID=' + data.key + '">Edit Permissions<\/a><\/li>';
			}
			/*
			if (data.treeNodeParentID > 0 && data.treeNodeTypeHandle == 'topic' && data.canDeleteTreeNode) {
				menu += '<li><a class="dialog-launch" dialog-width="550" dialog-on-open="$(\'[data-topic-form=remove-tree-node]\').ccmtopicstree(\'initRemoveNodeForm\', ' + options.treeID + ');" dialog-height="140" dialog-modal="false" dialog-title="Remove" href="' + CCM_TOOLS_PATH + '/tree/node/remove?treeNodeID=' + data.key + '">Delete Topic<\/a><\/li>';
			}
			*/

			menu += '</ul></div></div></div>';
			var $menu = $(menu);
			if ($menu.find('li').length == 0) {
				return false;
			}

			return $menu;

		},
    	

    	reloadNode: function(node, onComplete) {
    		var obj = this;
    		var params = {
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
	    					ccmAlert.notice('Error', '<div class="alert alert-danger">' + r.messages.join("<br>") + '</div>');
	    				} else {
	    					jQuery.fn.dialog.closeTop();
	    					onSuccess(r);
	    				}
	    			},
	    			error: function(r) {
    					ccmAlert.notice('Error', '<div class="alert alert-danger">' + r.responseText + '</div>');
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
    		var $tree = $('[data-group-tree=' + treeID + ']');
    		if (r.length) {
    			for (i = 0; i < r.length; i++) {
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

    initUpdateGroupNodeForm: function(treeID) {
    	methods.private.setupDialogForm($(this), function(r) {
    		var $tree = $('[data-group-tree=' + treeID + ']');
    		var node = $tree.dynatree('getTree').getNodeByKey(r.key);
    		node.data = r;
    		node.render();
    	});
    },


    initRemoveNodeForm: function(treeID) {
    	methods.private.setupDialogForm($(this), function(r) {
    		var $tree = $('[data-group-tree=' + treeID + ']');
    		var node = $tree.dynatree('getTree').getNodeByKey(r.treeNodeID);
    		node.remove();
    	});
    },

	init: function(options) {
		if(!options.noMenu) {
    		$.fn.ccmmenu.enable();
    	}
		var options = $.extend({
			readonly: false,
			chooseNodeInForm: false,
			onSelect: false,
			onClick: false,
			selectNodeByKey: false,
			removeNodesByID: [],
			disableDragAndDrop: false
		}, options);

		var checkbox = false,
			classNames = false;

		if(!options.treeNodeParentID) {
			var ajaxData = { 'treeID': options.treeID };
		} else {
			var ajaxData = { 'treeNodeParentID': options.treeNodeParentID };
		}

		var persist = true;

		if (options.chooseNodeInForm) {
			checkbox = true;
			persist = false;
			classNames = {
				'checkbox': 'dynatree-radio'
			};
			if (options.selectNodeByKey) {
				ajaxData.treeNodeSelectedID = options.selectNodeByKey;
			}
		}
		
		if (options.chooseNodeInForm === 'multiple') {
			checkbox = true;
			persist = false;
			classNames = {
				'checkbox': 'dynatree-checkbox'
			};
			if (options.selectNodeByKey) {
				ajaxData.treeNodeSelectedID = options.selectNodeByKey;
			}
		}
		
		if(options.allChildren) {
			ajaxData.allChildren = true;
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
					data: ajaxData, 
				},
				onLazyRead: function(node) {
					methods.private.reloadNode(node);
				},

				onPostInit: function() {
		    		var $tree = $obj;
		    		if (options.removeNodesByID.length) {
		    			for (i = 0; i < options.removeNodesByID.length; i++) {
		    				var nodeID = options.removeNodesByID[i];
		    				var node = this.getNodeByKey(nodeID);
		    				if (node) {
		    					node.remove();
		    				}
		    			}
		    		}
					if (options.readonly) {
			    		$tree.dynatree('disable');
					}

					if (options.chooseNodeInForm) {
						var selectedNodes = $tree.dynatree('getTree');
						var selectedNodes = selectedNodes.getSelectedNodes();
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
					if (options.onClick && node.getEventTargetType(e) == 'title') {
						options.onClick(e, node);
						return false;
					}

					$.fn.ccmmenu.hide(e);
					if (node.getEventTargetType(e) == 'expander') {
						return true;
					}
					if (options.chooseNodeInForm && node.getEventTargetType(e) != 'checkbox') {
						return false;
					}
					if (!node.getEventTargetType(e)) {
						return false;
					}
					if (!options.chooseNodeInForm && node.getEventTargetType(e) == 'title') {
						var $menu = methods.private.getMenu(node.data, options);
						if ($menu) {
							$.fn.ccmmenu.showmenu(e, $menu);
						}
					}
				},
				dnd: {
					onDragStart: function(node) {
						if(options.disableDragAndDrop) {
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
						// can't drag this thing out of the tree.
				        if (node.data.treeNodeParentID == '0' && hitMode != 'over') {
				        	return false;
				        }
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

  $.fn.ccmgroupstree = function(method) {

    if ( methods[method] ) {
      return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.ccmgroupstree' );
    }   

  };
})(jQuery, window);




/**
 * Menuing functions
 */



$.fn.ccmmenu = function() {
	
	$.fn.ccmmenu.enable();

	return $.each($(this), function(i, obj) {
		var $this = $(obj), 
			$menulauncher;

		if (!$this.prop('has-menu')) {
			$this.prop('has-menu', true);

			if (!$this.attr('data-menu-handle')) {
				$menulauncher = $this;
			} else {
				$menulauncher = $($this.attr('data-menu-handle'));
			}

			var $menu = $('#' + $this.attr('data-menu'));
			$this.$menu = $menu;
			$this.highlightClass = $this.attr('data-menu-highlight-class');
			$this.highlightOffset = 9;
			if ($this.attr('data-menu-highlight-offset')) {
				$this.highlightOffset = $this.attr('data-menu-highlight-offset');
			}
			$menulauncher.on('mousemove.ccmmenu', function(e) {
				//e.stopPropagation(); 
				// we had the above line in here so we could do some area menu niceties but it was playing hell with the gathering block.
				$.fn.ccmmenu.over(e, $this, $(this));
			});
		}
	});
};

$.fn.ccmmenu.resetHighlight = function() {
	$.fn.ccmmenu.$highlighter.hide();
	// remove any highlight classes
	$('[data-menu-highlight-class]').each(function() {
		var className = $(this).attr('data-menu-highlight-class');
		$('.' + className).removeClass(className);
	});
};

$.fn.ccmmenu.highlight = function($obj) {

	// we offset this because we're using outlines in the page and we want the highlighter to show up over the items.
	var offset = $obj.offset();
	var t = offset.top - $.fn.ccmmenu.$overmenu.highlightOffset;
	var l = offset.left - $.fn.ccmmenu.$overmenu.highlightOffset;
	var w = $obj.outerWidth() + ($.fn.ccmmenu.$overmenu.highlightOffset * 2);
	var h = $obj.outerHeight() + ($.fn.ccmmenu.$overmenu.highlightOffset * 2);

	$.fn.ccmmenu.$highlighter.css('width', w)
	.css('height', h)
	.css('top', t)
	.css('left', l)
	.css('border-top-left-radius', $obj.css('border-top-left-radius'))
	.css('border-bottom-left-radius', $obj.css('border-bottom-left-radius'))
	.css('border-top-right-radius', $obj.css('border-top-right-radius'))
	.css('border-bottom-right-radius', $obj.css('border-bottom-right-radius'))
	.removeClass().addClass($.fn.ccmmenu.$overmenu.highlightClass);

	$.fn.ccmmenu.$highlighter.show();
};

$.fn.ccmmenu.out = function(e) {
	if (!$.fn.ccmmenu.isactive) {
		$.fn.ccmmenu.$proxy.css("opacity", 0);
		$('.ccm-parent-menu-item-active').removeClass('ccm-parent-menu-item-active');
		$('.ccm-menu-item-active').removeClass('ccm-menu-item-active');
	}
};

/** 
 * Called especially after a delete, makes sure we're not screwing about with dom elements
 * that aren't there anymore
 */

$.fn.ccmmenu.reset = function() {
	$.fn.ccmmenu.disable();
	$.fn.ccmmenu.enable();
};

$.fn.ccmmenu.enable = function() {
	$.fn.ccmmenu.isenabled = true;
	if ($("#ccm-menu-click-proxy").length == 0) {
		$(document.body).append($("<div />", {'id': 'ccm-menu-click-proxy'}));
	}
	if ($("#ccm-menu-highlighter").length == 0) {
		$(document.body).append($("<div />", {'id': 'ccm-menu-highlighter'}));
	}

	if ($("#ccm-popover-menu-container").length == 0) {
		$(document.body).append($("<div />", {'id': 'ccm-popover-menu-container', 'class': 'ccm-ui'}));
	}
	$.fn.ccmmenu.$proxy = $('#ccm-menu-click-proxy');
	$.fn.ccmmenu.$highlighter = $('#ccm-menu-highlighter');
	$.fn.ccmmenu.$holder = $('#ccm-popover-menu-container');

	$.fn.ccmmenu.$proxy.on('mouseout.clickproxy', function(e) {
		$.fn.ccmmenu.out(e);
	});

	$.fn.ccmmenu.$proxy.on('mouseover.clickproxy', function(e) {
		$.fn.ccmmenu.over(e);
	});

	$.fn.ccmmenu.$proxy.unbind('click.clickproxy').on('click.clickproxy', function(e) {
		$.fn.ccmmenu.showmenu(e, $.fn.ccmmenu.$overmenu.$menu);
		$.fn.ccmmenu.highlight($.fn.ccmmenu.$overmenu);
		$.fn.ccmmenu.$overmenu.addClass($.fn.ccmmenu.$overmenu.highlightClass);
	});
};

$.fn.ccmmenu.disable = function() {
	$.fn.ccmmenu.out();
	$.fn.ccmmenu.isenabled = false;
	$.fn.ccmmenu.$proxy.remove();
	$.fn.ccmmenu.resetHighlight();
};

$.fn.ccmmenu.over = function(e, $this, $menulauncher) {

	if ($.fn.ccmmenu.isenabled && (!$.fn.ccmmenu.isactive)) {

		$('.ccm-menu-item-active').removeClass('ccm-menu-item-active');
		$('.ccm-parent-menu-item-active').removeClass('ccm-parent-menu-item-active');

		if ($menulauncher) {

			// we offset this because we're using outlines in the page and we want the highlighter to show up over the items.
			var offset = $menulauncher.offset();
			var t = offset.top - 5;
			var l = offset.left - 5;
			var w = $menulauncher.outerWidth() + 10;
			var h = $menulauncher.outerHeight() + 10;

			$.fn.ccmmenu.$proxy.css('width', w)
			.css('height', h)
			.css('top', t)
			.css('left', l)
			.css('border-top-left-radius', $menulauncher.css('border-top-left-radius'))
			.css('border-bottom-left-radius', $menulauncher.css('border-bottom-left-radius'))
			.css('border-top-right-radius', $menulauncher.css('border-top-right-radius'))
			.css('border-bottom-right-radius', $menulauncher.css('border-bottom-right-radius'));

			$.fn.ccmmenu.$overmenu = $this;
		}
		$.fn.ccmmenu.$overmenu.addClass('ccm-menu-item-active');
		$.fn.ccmmenu.$overmenu.parent().addClass('ccm-parent-menu-item-active');
	}
};

$.fn.ccmmenu.hide = function(e) {
	if (e) {
		e.stopPropagation();
	}
	if ($.fn.ccmmenu.$proxy) {
		$.fn.ccmmenu.isactive = false;
		$.fn.ccmmenu.$proxy.css("opacity", 0);
		$.fn.ccmmenu.resetHighlight();
		$.fn.ccmmenu.$holder.html('');
		$('.ccm-menu-item-active').removeClass('ccm-menu-item-active');
		$('.ccm-parent-menu-item-active').removeClass('ccm-parent-menu-item-active');
		$(document).unbind('click.disableccmmenu');
		$('div.popover').css('opacity', 0).hide();
	}
};

$.fn.ccmmenu.showmenu = function(e, $menu) {

	e.stopPropagation();

	if ($.fn.ccmmenu.isactive) {
		$('div.popover').css('opacity', 0).hide();
	}
	$.fn.ccmmenu.isactive = true;

	var $pp = $menu.clone(true, true);
	$pp.appendTo($.fn.ccmmenu.$holder);
	$pp.find('.dialog-launch').dialog();

	var posX = e.pageX + 2;
	var posY = e.pageY + 2;

	$pp.css('opacity', 0).show();
	var mheight = $pp.height(),
		mwidth = $pp.width();

	if ($(window).height() < (e.clientY + mheight + 30)) {
		posY = posY - mheight - 10;
		posX = posX - (mwidth / 2);
		$pp.removeClass('bottom');
		$pp.addClass('top');
	} else {
		posX = posX - (mwidth / 2);
		posY = posY + 10;
		$pp.removeClass('top');
		$pp.addClass('bottom');
	}	

	$pp.css("top", posY + "px");
	$pp.css("left", posX + "px");				
	$pp.show().css('opacity', 1);

	$pp.find('a').click(function(e) {
		$.fn.ccmmenu.hide(e);
	});

	$(document).on('click.disableccmmenu', function(e) {
		$.fn.ccmmenu.hide(e);
	});

};