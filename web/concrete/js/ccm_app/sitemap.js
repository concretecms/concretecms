/**
 * Sitemap proxy functions to dynatree
 */


(function($, window) {

  var methods = {

    private:  {


    },

    init: function(options) {

		return this.each(function() {
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