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
			pollRetryTimeout: 5000,
			$element: null
		}, options);
		my.options = options;
		my.lastRemaining = null;
		my.processed = 0;
		my.execute();
	}

	ConcreteProgressiveOperation.prototype.poll = function(queue, token, processed, remaining) {
		var my = this,
			total = processed + remaining;
			url = CCM_DISPATCHER_FILENAME + '/ccm/system/queue/monitor/' + queue + '/' + token + '/' + processed;
		if (remaining) {
			if (my.lastRemaining) {
				my.processed += my.lastRemaining - remaining;
			}
			my.lastRemaining = remaining;
		}
		$.concreteAjax({
			loader: false,
			url: url,
			type: 'POST',
			dataType: 'json',
			success: function(r) {
				var pnotify = new PNotify({
					text: '<div data-total-items="' + total + '"><span id="ccm-progressive-operation-status">1</span> of ' + total + '</div>',
					hide: false,
					title: my.options.title,
					buttons: {
						closer: false
					},
					type: 'info',
					icon: 'fa fa-refresh fa-spin'
				});

				setTimeout(function() {
					my.poll(queue, token, 0, r.remaining);
				}, my.options.pollRetryTimeout);
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
				my.poll(r.queue, r.token, 0);
			}
		});
	}

	global.ConcreteProgressiveOperation = ConcreteProgressiveOperation;

}(this, $);