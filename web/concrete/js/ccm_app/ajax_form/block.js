/**
 * block ajax
 */

!function(global, $) {
	'use strict';

	function ConcreteAjaxBlockForm($form, options) {
		'use strict';
		var my = this;
		options = $.extend({
			'iframe': true,
			'task': false,
			'bID': false
		}, options);
		my.options = options;

		return ConcreteAjaxForm.call(my, $form, options);
	}

	ConcreteAjaxBlockForm.prototype = Object.create(ConcreteAjaxForm.prototype);

	ConcreteAjaxBlockForm.prototype.beforeSubmit = function(my) {
		$('input[name=ccm-block-form-method]').val('AJAX');
		ConcreteAjaxForm.prototype.beforeSubmit.call(this, my);
	};

	ConcreteAjaxBlockForm.prototype.success = function(resp, my) {
		if (my.validateResponse(resp)) {
			var cID = (resp.cID) ? resp.cID : CCM_CID,
				action = CCM_TOOLS_PATH + '/edit_block_popup?cID=' + cID + '&bID=' + resp.bID + '&arHandle=' + encodeURIComponent(resp.arHandle) + '&btask=view_edit_mode';

			jQuery.fn.dialog.closeTop();

			$.get(action, function(r) {
				if (my.task == 'add') {
					$("#a" + resp.aID + " > div.ccm-area-block-list").append(r);
				} else {
					$('[data-block-id=' + my.options.bID + '][data-area-id=' + resp.aID + ']').before(r).remove();
				}

				CCMInlineEditMode.exit();
				CCMToolbar.disableDirectExit();
				jQuery.fn.dialog.hideLoader();

				if (my.task == 'add') {
					var tb = parseInt($('div.ccm-area[data-area-id=' + resp.aID + ']').attr('data-total-blocks'));
					$('div.ccm-area[data-area-id=' + resp.aID + ']').attr('data-total-blocks', tb + 1);
					ccmAlert.hud(ccmi18n.addBlockMsg, 2000, 'ok', ccmi18n.addBlock);
					jQuery.fn.dialog.closeAll();
				} else {
					ccmAlert.hud(ccmi18n.updateBlockMsg, 2000, 'ok', ccmi18n.updateBlock);
				}
				c5.editMode.scanBlocks();
				$.fn.ccmmenu.reset();
			});
		}
	}

	// jQuery Plugin
	$.fn.concreteAjaxBlockForm = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteAjaxBlockForm($(this), options);
		});
	}

	global.ConcreteAjaxBlockForm = ConcreteAjaxBlockForm;

}(this, $);