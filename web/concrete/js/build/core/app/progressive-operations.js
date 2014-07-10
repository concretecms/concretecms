/** 
 * progress bar
 */

ccm_triggerProgressiveOperation = function(url, params, dialogTitle, onComplete, onError) {
	jQuery.fn.dialog.showLoader();
	$('#ccm-dialog-progress-bar').remove();
	$.ajax({
		url: url,
		type: 'POST',
		data: params, 
		success: function(r) {
			jQuery.fn.dialog.hideLoader();
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
		}
	});
}

ccm_doProgressiveOperation = function(url, params, totalItems, onComplete, onError) {
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
			$('#ccm-dialog-progress-bar').dialog('option', 'height', 200);
			$('#ccm-dialog-progress-bar').dialog('option', 'closeOnEscape', true);
			$('#ccm-progressive-operation-progress-bar').html('<div class="alert alert-error">' + text + '</div>');
			$('.ui-dialog-titlebar-close').show();
		},

		success: function(r) {
			if (r.error) {
				var text = r.message;
				$('#ccm-dialog-progress-bar').dialog('option', 'height', 200);
				$('#ccm-dialog-progress-bar').dialog('option', 'closeOnEscape', true);
				$('#ccm-progressive-operation-progress-bar').html('<div class="alert alert-error">' + text + '</div>');
				$('.ui-dialog-titlebar-close').show();
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
						$('#ccm-dialog-progress-bar').dialog('close');
						if (typeof(onComplete) == 'function') {
							onComplete(r);
						}
					}, 1000);
				}
			}
		}
	});
}