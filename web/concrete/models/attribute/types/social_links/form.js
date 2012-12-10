$(function() {
	$('.ccm-social-link-attribute-add-service').click(function() {
		$($('.ccm-social-link-attribute').get(0)).
			clone().
			appendTo($('.ccm-social-link-attribute-wrapper')).
			find('input').
			val('').
			parent().
			find('select').
			trigger('change');
		$('button.ccm-social-link-attribute-remove-line').show();
		$('button.ccm-social-link-attribute-remove-line:first').hide();
	});
	$('.ccm-social-link-attribute-wrapper').on('click', 'button.ccm-social-link-attribute-remove-line', function() {
		$(this).parent().remove();
	});
	$('.ccm-social-link-attribute-wrapper').on('change', 'select', function() {
		var opt = $($(this).find(':selected'));
		
		$('button.ccm-social-link-attribute-remove-line').show();
		$('button.ccm-social-link-attribute-remove-line:first').hide();
					
		$(this).parent().find('input').attr('placeholder', opt.attr('data-placeholder-text'));
		$(this).parent().
			find('.ccm-social-link-service-text-wrapper').
			addClass('input-prepend').
			find('.ccm-social-link-service-add-on-wrapper').show().
			find('img').
			attr('src', CCM_IMAGE_PATH + "/icons/social/" + opt.val() + ".png");

	});
	$('.ccm-social-link-attribute-wrapper select').trigger('change');
});