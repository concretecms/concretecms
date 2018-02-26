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
			pollRetryTimeout: 1000,
			$element: null
		}, options);
		my.options = options;
		my.current = 0;
		my.total = -1; // unknown
		my.pnotify = false;
		my.execute();
	}

	ConcreteProgressiveOperation.prototype.poll = function(queue, token, remaining) {
		var my = this,
			url = CCM_DISPATCHER_FILENAME + '/ccm/system/queue/monitor/' + queue + '/' + token;

		$.concreteAjax({
			loader: false,
			url: url,
			type: 'POST',
			dataType: 'json',
			success: function(r) {

				if (my.total == -1) {
					// We haven't set the total yet.
					my.total = r.remaining;
				}

				my.current += my.total - r.remaining;
				NProgress.set((my.total - r.remaining) / my.total);

				$('div[data-wrapper=progressive-operation-status]').html(r.remaining + ' remaining');

				if (r.remaining > 0) {
					setTimeout(function() {
						my.poll(queue, token, r.remaining);
					}, my.options.pollRetryTimeout);
				} else {
					setTimeout(function() {
						// give the animation time to catch up.
						NProgress.done();
						my.pnotify.remove();
						if (typeof(my.options.onComplete) == 'function') {
							my.options.onComplete(r);
						}
					}, 1000);

				}
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

				my.pnotify = new PNotify({
					text: '<div data-wrapper="progressive-operation-status">' + ccmi18n.progressiveOperationLoading + '</div>',
					hide: false,
					title: my.options.title,
					buttons: {
						closer: false
					},
					type: 'info',
					icon: 'fa fa-refresh fa-spin'
				});

				my.poll(r.queue, r.token);
			}
		});
	}

	global.ConcreteProgressiveOperation = ConcreteProgressiveOperation;

}(this, $);