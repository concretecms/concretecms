;(function(global, $) {
    'use strict';

	function ConcreteProgressiveOperation(options) {
		var my = this;
		options = $.extend({
			url: '',
			data: {},
			title: '',
			response: null, // If we have already performed the queueing action, as in a form, we will have a response, and no URL/data
			onComplete: null,
			onError: null,
			pollRetryTimeout: 1000,
			element: null
		}, options);
		my.options = options;
		my.current = 0;
		my.total = -1; // unknown
		my.pnotify = false;
		my.execute();
	}

	ConcreteProgressiveOperation.prototype.setProgressBarStatus = function(completion, remaining) {
		var my = this,
			$remainingElement = my.options.element.find('div[data-progress-bar=remaining]');

		if (remaining > -1) {
			my.options.element.find('div.progress').removeClass('progress-striped active');
			my.options.element.find('div.progress-bar').css('width', completion + '%');

			if (!$remainingElement.length) {
				my.options.element.append('<div data-progress-bar="remaining"></div>');
				$remainingElement = my.options.element.find('div[data-progress-bar=remaining]');
			}
			$remainingElement.html(remaining + ' remaining');
		} else {
			my.options.element.find('div.progress').addClass('progress-striped active');
			my.options.element.find('div.progress-bar').css('width', '100%');
		}
	};

	ConcreteProgressiveOperation.prototype.poll = function(queue, token, remaining) {
		var my = this,
			url = CCM_DISPATCHER_FILENAME + '/ccm/system/queue/monitor/' + queue + '/' + token;

		if (my.total == -1) {
			// We haven't set the total yet.
			my.total = remaining;
		}

		my.current += my.total - remaining;

		if (!my.options.element) {
			NProgress.set((my.total - remaining) / my.total);
			$('div[data-wrapper=progressive-operation-status]').html(remaining + ' remaining');
		} else {
			var completion = ((my.total - remaining) / my.total) * 100;
			my.setProgressBarStatus(completion, remaining);
		}

		$.concreteAjax({
			loader: false,
			url: url,
			type: 'POST',
			dataType: 'json',
			success: function(r) {

				if (r.remaining > 0) {
					setTimeout(function() {
						my.poll(queue, token, r.remaining);
					}, my.options.pollRetryTimeout);
				} else {
					setTimeout(function() {
						if (my.options.element) {
							my.setProgressBarStatus(100, 0);
						} else {
							NProgress.done();
							my.pnotify.remove();
						}
						if (typeof(my.options.onComplete) == 'function') {
							my.options.onComplete(r);
						}
					}, 1000);

				}
			}
		});
	};

	ConcreteProgressiveOperation.prototype.startPolling = function(queue, token, remaining) {
		var my = this;
		if (!my.options.element) {

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
		}

		my.poll(queue, token, remaining);
	};

	ConcreteProgressiveOperation.prototype.execute = function() {
		var my = this;
		if (my.options.element) {
			my.setProgressBarStatus(0, -1);
		} else {
			NProgress.set(0);
		}

		if (my.options.response) {
			// We have already performed the submit as part of another operation,
			// like a concrete5 ajax form submission
			my.startPolling(my.options.response.queue, my.options.response.token, my.options.response.remaining);
		} else {
			$.concreteAjax({
				loader: false,
				url: my.options.url,
				type: 'POST',
				data: my.options.data,
				dataType: 'json',
				success: function(r) {
					my.startPolling(r.queue, r.token, r.remaining);
				}
			});
		}
	};

	global.ConcreteProgressiveOperation = ConcreteProgressiveOperation;

})(this, jQuery);
