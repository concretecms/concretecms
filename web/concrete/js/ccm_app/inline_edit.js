/** 
 * Inline Edit Mode
 */


var CCMInlineEditMode = function() {

	enterInlineEditMode = function(activeObj) {
		
		$.fn.ccmmenu.disable();
		$('#ccm-toolbar-disabled').remove();
		$('<div />', {'id': 'ccm-toolbar-disabled'}).appendTo(document.body);
		setTimeout(function() {
			$('#ccm-toolbar-disabled').css('opacity', 1);
		}, 10);
		$('div.ccm-block-edit').addClass('ccm-block-edit-disabled');
		$('div.ccm-area').removeClass('ccm-area').addClass('ccm-area-disabled');
		$('div.ccm-area-footer-handle').addClass('ccm-block-edit-disabled');
		$('div.ccm-area-layout-control-bar').addClass('ccm-block-edit-disabled');
		if (activeObj) {
			activeObj.removeClass('ccm-block-edit-disabled').addClass('ccm-block-edit-inline-active');
		}
	}

	completeInlineEditMode = function($block) {
		$('#ccm-inline-toolbar-container').remove();

		var $toolbar = $block.find('.ccm-inline-toolbar'),
			$holder = $('<div />', {id: 'ccm-inline-toolbar-container'}).appendTo(document.body),
			$window = $(window),
			pos = $block.offset();

		$toolbar.css('left', pos.left).css('opacity', 1).appendTo($holder);
		$toolbar.find('.dialog-launch').dialog();
		var t = pos.top - $holder.outerHeight();
		$holder.css('top', t).css('opacity', 1);

		if ($window.scrollTop() > t) {
			$('#ccm-toolbar-disabled,#ccm-toolbar').css('opacity', 0);
			$holder.addClass('ccm-inline-toolbar-affixed');
		}

		$window.on('scroll.inline-toolbar', function() {
			$holder.toggleClass('ccm-inline-toolbar-affixed', $window.scrollTop() > t);
			if ($window.scrollTop() > t) {
				$('#ccm-toolbar-disabled,#ccm-toolbar').css('opacity', 0);
			} else {
				$('#ccm-toolbar-disabled,#ccm-toolbar').css('opacity', 1);
			}
		});
	}

	return {

		exit: function(onComplete) {
			$(document).trigger('inlineEditCancel', [onComplete]);
		},

		finishExit: function() {
			$('div.ccm-area-edit-inline-active').removeClass('ccm-area-edit-inline-active');
			$.fn.ccmmenu.enable();
			$('div.ccm-block-edit-inline-active').remove();
			$(window).unbind('scroll.inline-toolbar');
			$('div.ccm-block-edit').removeClass('ccm-block-edit-disabled');
			$('div.ccm-area-footer-handle').removeClass('ccm-block-edit-disabled');
			$('div.ccm-area-layout-control-bar').removeClass('ccm-block-edit-disabled');
			$('div.ccm-area-disabled').removeClass('ccm-area-disabled').addClass('ccm-area');
			$('#ccm-toolbar-disabled').remove();
			$('#ccm-toolbar').css('opacity', 1);
			$('#ccm-inline-toolbar-container').remove();

			jQuery.fn.dialog.hideLoader();
			CCMEditMode.start();
		},

		editBlock: function(cID, aID, arHandle, bID, params) {
			
			var postData = [
				{name: 'btask', value: 'edit'},
				{name: 'cID', value: cID},
				{name: 'arHandle', value: arHandle},
				{name: 'aID', value: aID},
				{name: 'bID', value: bID}
			];

			if (params) {
				for (var prop in params) {
					postData.push({name: prop, value: params[prop]});
				}
			}

			jQuery.fn.dialog.showLoader();
			enterInlineEditMode($('[data-block-id=' + bID + '][data-area-id=' + aID + ']'));

			$.ajax({
			type: 'GET',
			url: CCM_TOOLS_PATH + '/edit_block_popup',
			data: postData,
			success: function(r) {
				var $container = $('[data-block-id=' + bID + '][data-area-id=' + aID + ']');
				$container.html(r);
				completeInlineEditMode($container);
				jQuery.fn.dialog.hideLoader();
			}});
		},

		loadAdd: function(cID, arHandle, aID, btID, params) {
			var postData = [
				{name: 'btask', value: 'edit'},
				{name: 'cID', value: cID},
				{name: 'arHandle', value: arHandle},
				{name: 'btID', value: btID}
			];

			if (params) {
				for (var prop in params) {
					postData.push({name: prop, value: params[prop]});
				}
			}

			jQuery.fn.dialog.showLoader();
			enterInlineEditMode();
			$.ajax({
			type: 'GET',
			url: CCM_TOOLS_PATH + '/add_block_popup',
			data: postData,
			success: function(r) {
				jQuery.fn.dialog.closeAll();
				if ($('#ccm-add-new-block-placeholder').length > 0) {
					var obj = $('#ccm-add-new-block-placeholder');
				} else {
					var obj = $('#a' + aID);
				}
				obj.addClass("ccm-area-edit-inline-active");
				obj.append($('<div id="a' + aID + '-bt' + btID + '" class="ccm-block-edit-inline-active">' + r + '</div>'));
				jQuery.fn.dialog.hideLoader();
			}});
		}

	}


}();