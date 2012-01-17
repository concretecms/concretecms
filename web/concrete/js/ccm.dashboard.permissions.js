
$(function() {	
	$(".ug-selector").dialog();	
	ccm_sitemapActivatePermissionsSelector();	

	ccm_triggerSelectUser = function(uID, uName) {
		ccm_sitemapSelectPermissionsEntity('uID', uID, uName);
	}
	
	ccm_triggerSelectGroup = function (gID, gName) {
		ccm_sitemapSelectPermissionsEntity('gID', gID, gName);
	}

});

ccm_sitemapSelectPermissionsEntity = function(selector, id, name) {
	var html = $('#ccm-permissions-entity-base').html();
	$('#ccm-permissions-entities-wrapper').append('<div class="ccm-permissions-entity">' + html + '<\/div>');
	var p = $('.ccm-permissions-entity');
	var ap = p[p.length - 1];
	$(ap).find('h3 span').html(name);
	$(ap).find('input[type=hidden]').val(selector + '_' + id);
	$(ap).find('input[type=radio]').each(function() {
		$(this).attr('name', selector + '_' + id + '_' + $(this).attr('name'));
	});
	
	ccm_sitemapActivatePermissionsSelector();	
}

ccm_sitemapActivatePermissionsSelector = function() {
	$("a.ccm-permissions-remove").click(function() {
		$(this).parent().parent().fadeOut(100, function() {
			$(this).remove();
		});
	});
}