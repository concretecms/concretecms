/**
 * Sitemap proxy functions to dynatree
 */


(function($, window) {

  var methods = {

    private:  {

    	getMenu: function(instanceID, data) {
    		var menu = '<div data-sitemap-instance-id="' + instanceID + '" class="ccm-sitemap-menu popover fade"><div class="arrow"></div><div class="popover-inner">';
    		menu += '<ul class="dropdown-menu">';
    		if (data.isTrash && data.numSubpages) {
    			menu += '<li><a onclick="$.fn.ccmsitemap(\'emptyTrash\', this)" href="javascript:void(0)">' + ccmi18n_sitemap.emptyTrash + '<\/a><\/li>';
    		}
    		menu += '</ul></div></div>';
    		var $menu = $(menu);
    		if ($menu.find('li').length == 0) {
    			return false;
    		}

    		return $menu;
    	}

    },

    emptyTrash: function(link) {
		var instanceID = $(link).closest('div.ccm-sitemap-menu').attr('data-sitemap-instance-id');
		var node = $('[data-sitemap-instance-id=' + instanceID + ']').dynatree('getActiveNode');
		node.setLazyNodeStatus(DTNodeStatus_Loading);
		$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_empty_trash.php', function(resp) {
			// parse response
			ccm_parseJSON(resp, function() {
				ccmAlert.hud(resp.message, 2000);
				node.data.numSubpages = 0;
				node.reloadChildren();
			});
		});

    },

    init: function(options) {

    	$('#ccm-show-all-pages-cb').on('click', function() {
			var showSystemPages = $(this).get(0).checked == true ? 1 : 0;
			$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?show_system=" + showSystemPages, function(resp) {
				location.reload();
			});
    	});
    	$.fn.ccmmenu.enable();
		return this.each(function() {
	    	var instanceID = $(this).attr("data-sitemap-instance-id");
			$(this).dynatree({
				autoFocus: false,
				cookieId: 'ccmsitemap',
				cookie: {
					path: CCM_REL + '/'
				},
				persist: true,
				initAjax: {
					url: CCM_TOOLS_PATH + '/dashboard/sitemap_data',
					data: {

					}
				},
				selectMode: 1,
				minExpandLevel: 1,
				clickFolderMode: 2,
				onLazyRead: function(node) {
					node.appendAjax({
						url: CCM_TOOLS_PATH + '/dashboard/sitemap_data',
						data: {
							node: node.data.cID
						}
					});
				}, 
				onClick: function(node, e) {
					if (node.getEventTargetType(event) == "title"){
						var $menu = methods.private.getMenu(instanceID, node.data);
						if ($menu) {
							$.fn.ccmmenu.showmenu(e, $menu);
						}
					}
				},
				dnd: {
					onDragStart: function(node) {
						return true;
					},
					onDragStop: function(node) {

					},
					autoExpandMS: 1000,
					preventVoidMoves: true,
					onDragEnter: function(node, sourceNode) {

						return true;
					},
					onDragOver: function(node, sourceNode, hitMode) {
				        // Prevent dropping a parent below it's own child
				        if(node.isDescendantOf(sourceNode)){
				          return false;
				        }
				        return true;
					},
					onDrop: function(node, sourceNode, hitMode, ui, draggable) {
				        sourceNode.move(node, hitMode);
					}
				}
			});
		});

    }


  }

  $.fn.ccmsitemap = function(method) {

    if ( methods[method] ) {
      return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.ccmsitemap' );
    }   

  };
})(jQuery, window);