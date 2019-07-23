/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global CCM_DISPATCHER_FILENAME */

;(function(global, $) {
	'use strict';

	var ConcreteThumbnailBuilder = {

		build: function() {
			$.post(CCM_DISPATCHER_FILENAME + '/ccm/system/file/thumbnailer', function(result) {
				if (result.built === true) {
					if (result.path) {
						$('[src$="' + result.path + '"]').each(function() {
							var me = $(this);
							me.attr('src', me.attr('src'));
						});
					}
					setTimeout(ConcreteThumbnailBuilder.build, 50);
				}
			});
		}

	};

	ConcreteThumbnailBuilder.build();

})(this, jQuery);
