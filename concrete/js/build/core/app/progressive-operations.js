/** 
 * progress bar
 */

ccm_triggerProgressiveOperation = function(url, params, dialogTitle, onComplete, onError, $element) {
	// $element lets us pass in a DOM object to get the progress bar, as opposed to popping open a dedicated dialog for it.
	$('#ccm-dialog-progress-bar').remove();
	if (!$element) {
		NProgress.set(0);
	}
	$.concreteAjax({
		loader: false,
		url: url,
		type: 'POST',
		data: params,
		dataType: 'html',
		success: function(r) {
			if (!$element) {
				/*
				$('<div id="ccm-dialog-progress-bar" />').appendTo(document.body).html(r).jqdialog({
					autoOpen: false,
					height: 200,
					width: 400,
					modal: true,
					title: dialogTitle,
					closeOnEscape: false,
					open: function(e, ui) {
						$('.ui-dialog-titlebar-close', this.parentNode).hide();
						var totalItems = $('#ccm-progressive-operation-progress-bar').attr('data-total-items');
						ccm_doProgressiveOperation(url, params, totalItems, onComplete, onError);
					}
				});
				$("#ccm-dialog-progress-bar").jqdialog('open');
				*/
				var $bar = $(r);
				var totalItems = $bar.find('[data-total-items]').attr('data-total-items');
				var pnotify = new PNotify({
					text: '<div data-total-items="' + totalItems + '"><span id="ccm-progressive-operation-status">1</span> of ' + totalItems + '</div>',
					hide: false,
					title: dialogTitle,
					buttons: {
						closer: false
					},
					type: 'info',
					icon: 'fa fa-refresh fa-spin'
				});

				ccm_doProgressiveOperation(url, params, totalItems, onComplete, onError, pnotify);



			} else {
				$element.html(r);
				var totalItems = $('#ccm-progressive-operation-progress-bar').attr('data-total-items');
				ccm_doProgressiveOperation(url, params, totalItems, onComplete, onError, $element);
			}
		}
	});
}

ccm_doProgressiveOperation = function(url, params, totalItems, onComplete, onError, container) {
	params.push({
		'name': 'process',
		'value': '1'
	});
	var pnotify, $element;
	if (container instanceof jQuery) {
		$element = container;
	} else {
		pnotify = container;
	}
	params['process'] = true;
	$.ajax({
		url: url,
		dataType: 'json',
		type: 'POST',
		data: params,
		error:function(xhr, status, r) {
			switch(status) {
				case 'timeout':
					var text = '<div class="alert alert-danger">' + ccmi18n.requestTimeout + '</div>';
					break;
				default:
					var text = ConcreteAjaxRequest.errorResponseToString(xhr);
					break;
			}
			if (!$element) {
				pnotify.remove();
				NProgress.remove();
			}
			ConcreteAlert.dialog(ccmi18n.error, text);
		},

		success: function(r) {
			if (r.error) {
				var text = r.message;
				if (!$element) {
					pnotify.remove();
					NProgress.remove();
				}
				ConcreteAlert.dialog(ccmi18n.error, text);

				if (typeof(onError) == 'function') {
					onError(r);
				}
			} else {
				var totalItemsLeft = r.totalItems;
				// update the percentage
				var pct = (totalItems - totalItemsLeft) / totalItems;
				$('#ccm-progressive-operation-status').html(1);
				if ((totalItems - totalItemsLeft) > 0) {
					$('#ccm-progressive-operation-status').html(totalItems - totalItemsLeft);
				}
				if ($element) {
					pct = Math.round(pct * 100);
					$('#ccm-progressive-operation-progress-bar div.progress-bar').width(pct + '%');
				} else {
					NProgress.set(pct);
				}
				if (totalItemsLeft > 0) {
					setTimeout(function() {
						ccm_doProgressiveOperation(url, params, totalItems, onComplete, onError, container);
					}, 250);
				} else {
					setTimeout(function() {
						// give the animation time to catch up.
						if ($element) {
							$('#ccm-progressive-operation-progress-bar div.bar').width('0%');
						} else {
							NProgress.done();
							pnotify.remove();
						}
						if (typeof(onComplete) == 'function') {
							onComplete(r);
						}
					}, 1000);
				}
			}
		}
	});
}