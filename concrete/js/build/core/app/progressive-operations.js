/** 
 * progress bar
 */

ccm_triggerProgressiveOperation = function(url, params, dialogTitle, onComplete, onError, $element) {
	// $element lets us pass in a DOM object to get the progress bar, as opposed to popping open a dedicated dialog for it.
	$('#ccm-dialog-progress-bar').remove();
	$.concreteAjax({
		loader: false,
		url: url,
		type: 'POST',
		data: params,
		dataType: 'html',
		success: function(r) {
			jQuery.fn.dialog.hideLoader();
			if (!$element) {
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
			} else {
				$element.html(r);
				var totalItems = $('#ccm-progressive-operation-progress-bar').attr('data-total-items');
				ccm_doProgressiveOperation(url, params, totalItems, onComplete, onError, $element);
			}
		}
	});
}

ccm_doProgressiveOperation = function(url, params, totalItems, onComplete, onError, $element) {
	params.push({
		'name': 'process',
		'value': '1'
	});
	params['process'] = true;
	$.ajax({
		url: url,
		dataType: 'json',
		type: 'POST',
		data: params,
		error:function(xhr, status, r) {
			switch(status) {
				case 'timeout':
					var text = ccmi18n.requestTimeout;
					break;
				default:
					var text = xhr.responseText;
					break;
			}
			if (!$element) {
				$('#ccm-dialog-progress-bar').dialog('option', 'height', 200);
				$('#ccm-dialog-progress-bar').dialog('option', 'closeOnEscape', true);
				$('.ui-dialog-titlebar-close').show();
			}
			if ($element) {
				$element.html('<div class="alert alert-error">' + text + '</div>');
			} else {
				$('#ccm-progressive-operation-progress-bar').html('<div class="alert alert-error">' + text + '</div>');
			}
		},

		success: function(r) {
			if (r.error) {
				if (!$element) {
					$('#ccm-dialog-progress-bar').dialog('option', 'height', 200);
					$('#ccm-dialog-progress-bar').dialog('option', 'closeOnEscape', true);
					$('.ui-dialog-titlebar-close').show();
				}
				var text = r.message;
				$('#ccm-progressive-operation-progress-bar').html('<div class="alert alert-error">' + text + '</div>');
				if (typeof(onError) == 'function') {
					onError(r);
				}
			} else {
				var totalItemsLeft = r.totalItems;
				// update the percentage
				var pct = Math.round(((totalItems - totalItemsLeft) / totalItems) * 100);
				$('#ccm-progressive-operation-status').html(1);
				if ((totalItems - totalItemsLeft) > 0) {
					$('#ccm-progressive-operation-status').html(totalItems - totalItemsLeft);
				}
				$('#ccm-progressive-operation-progress-bar div.progress-bar').width(pct + '%');
				if (totalItemsLeft > 0) {
					setTimeout(function() {
						ccm_doProgressiveOperation(url, params, totalItems, onComplete, onError);
					}, 250);
				} else {
					setTimeout(function() {
						// give the animation time to catch up.
						$('#ccm-progressive-operation-progress-bar div.bar').width('0%');
						if (!$element) {
							$('#ccm-dialog-progress-bar').dialog('close');
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