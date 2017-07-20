!function (global, $, _) {
	'use strict';

	var ConcreteThumbnailBuilder = {

		build: function() {
			$.post(CCM_DISPATCHER_FILENAME + '/ccm/system/file/thumbnailer', function(result) {
				if (result.built === true) {
					if (result.path) {
						$('[src$="' + result.path + '"]').each(function() {
							var me = $(this);
							me.attr('src', me.attr('src'));
						})
					}
					setTimeout(ConcreteThumbnailBuilder.build, 50);
				}
			});
		}

	}

	ConcreteThumbnailBuilder.build();

}(this, $, _);
