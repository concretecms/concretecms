!function (global, $) {
	'use strict';

	function ConcreteProgressiveOperation(options) {
		'use strict';
		var my = this;
		options = $.extend({
			url: '',
			data: {},
			title: '',
			onComplete: null,
			onError: null,

			$element: null
		}, options);
		my.options = options;
		my.execute();
	}

	ConcreteProgressiveOperation.prototype.poll = function(queue, token) {
		var url = CCM_DISPATCHER_FILENAME + '/ccm/system/queue/monitor/' + queue + '/' + token;
		$.concreteAjax({
			loader: false,
			url: url,
			type: 'POST',
			dataType: 'json',
			success: function(r) {
				console.log(r);
			}
		});
	}

	ConcreteProgressiveOperation.prototype.execute = function() {
		var my = this;
		if (!my.options.$element) {
			NProgress.set(0);
		}

		$.concreteAjax({
			loader: false,
			url: my.options.url,
			type: 'POST',
			data: my.options.data,
			dataType: 'json',
			success: function(r) {
				my.poll(r.queue, r.token);
			}
		});
	}

	global.ConcreteProgressiveOperation = ConcreteProgressiveOperation;

}(this, $);