/**
 * Page search
 */
ccm_sitemapSetupSearch = function(instance_id) {
   	$.fn.ccmmenu.enable();
	ccm_setupAdvancedSearch(instance_id); 
	ccm_sitemapSetupSearchPages(instance_id);
	ccm_searchActivatePostFunction[instance_id] = function() {
		ccm_sitemapSetupSearchPages(instance_id);
		ccm_sitemapSearchSetupCheckboxes(instance_id);	
	}
	ccm_sitemapSearchSetupCheckboxes(instance_id);	
}

ccm_sitemapSearchSetupCheckboxes = function(instance_id) {
	$("#ccm-" + instance_id + "-list-cb-all").click(function(e) {
		e.stopPropagation();
		if ($(this).prop('checked') == true) {
			$('.ccm-list-record td.ccm-' + instance_id + '-list-cb input[type=checkbox]').attr('checked', true);
			$("#ccm-" + instance_id + "-list-multiple-operations").attr('disabled', false);
		} else {
			$('.ccm-list-record td.ccm-' + instance_id + '-list-cb input[type=checkbox]').attr('checked', false);
			$("#ccm-" + instance_id + "-list-multiple-operations").attr('disabled', true);
		}
	});
	$("td.ccm-" + instance_id + "-list-cb input[type=checkbox]").click(function(e) {
		e.stopPropagation();
		if ($("td.ccm-" + instance_id + "-list-cb input[type=checkbox]:checked").length > 0) {
			$("#ccm-" + instance_id + "-list-multiple-operations").attr('disabled', false);
		} else {
			$("#ccm-" + instance_id + "-list-multiple-operations").attr('disabled', true);
		}
	});
	
	// if we're not in the dashboard, add to the multiple operations select menu

	$("#ccm-" + instance_id + "-list-multiple-operations").change(function() {
		var action = $(this).val();
		cIDstring = '';
		$("td.ccm-" + instance_id + "-list-cb input[type=checkbox]:checked").each(function() {
			cIDstring=cIDstring+'&cID[]='+$(this).val();
		});
		switch(action) {
			case "delete":
				jQuery.fn.dialog.open({
					width: 500,
					height: 400,
					modal: false,
					appendButtons: true,
					href: CCM_TOOLS_PATH + '/pages/delete?' + cIDstring + '&searchInstance=' + instance_id,
					title: ccmi18n_sitemap.deletePages				
				});
				break;
			case "design":
				jQuery.fn.dialog.open({
					width: 610,
					height: 405,
					modal: false,
					appendButtons: true,
					href: CCM_TOOLS_PATH + '/pages/design?' + cIDstring + '&searchInstance=' + instance_id,
					title: ccmi18n_sitemap.pageDesign				
				});
				break;
			case 'move_copy':
				jQuery.fn.dialog.open({
					width: 640,
					height: 340,
					modal: false,
					href: CCM_TOOLS_PATH + '/sitemap_overlay?instance_id=' + instance_id + '&select_mode=move_copy_delete&' + cIDstring,
					title: ccmi18n_sitemap.moveCopyPage				
				});
				break;
			case 'speed_settings':
				jQuery.fn.dialog.open({
					width: 610,
					height: 340,
					modal: false,
					appendButtons: true,
					href: CCM_TOOLS_PATH + '/pages/speed_settings?' + cIDstring,
					title: ccmi18n_sitemap.speedSettingsTitle				
				});
				break;
			case 'permissions':
				jQuery.fn.dialog.open({
					width: 430,
					height: 630,
					modal: false,
					appendButtons: true,
					href: CCM_TOOLS_PATH + '/pages/permissions?' + cIDstring,
					title: ccmi18n_sitemap.pagePermissionsTitle				
				});
				break;
			case 'permissions_add_access':
				jQuery.fn.dialog.open({
					width: 440,
					height: 200,
					modal: false,
					appendButtons: true,
					href: CCM_TOOLS_PATH + '/pages/permissions_access?task=add&' + cIDstring,
					title: ccmi18n_sitemap.pagePermissionsTitle				
				});
				break;
			case 'permissions_remove_access':
				jQuery.fn.dialog.open({
					width: 440,
					height: 300,
					modal: false,
					appendButtons: true,
					href: CCM_TOOLS_PATH + '/pages/permissions_access?task=remove&' + cIDstring,
					title: ccmi18n_sitemap.pagePermissionsTitle				
				});
				break;
			case "properties": 
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: false,
					href: CCM_TOOLS_PATH + '/pages/bulk_metadata_update?' + cIDstring,
					title: ccmi18n_sitemap.pagePropertiesTitle				
				});
				break;				
		}
		
		$(this).get(0).selectedIndex = 0;
	});
}

ccm_sitemapSetupSearchPages = function(instance_id) {
	$('#ccm-' + instance_id + '-list tr').click(function(e){
		var node = $(this);
		if (node.hasClass('ccm-results-list-header')) {
			return false;
		}


		var $menu = $.fn.ccmsitemap('getMenu', jQuery.parseJSON($(this).attr('data-page-menu')));
		if ($menu) {
			$.fn.ccmmenu.showmenu(e, $menu);
		}

		/*
		if (node.attr('sitemap-select-mode') == 'select_page') {
			var callback = node.attr('sitemap-select-callback');
			if (callback == null || callback == '' || typeof(callback) == 'undefined') {
				callback = 'ccm_selectSitemapNode';
			}
			eval(callback + '(node.attr(\'cID\'), unescape(node.attr(\'cName\')));');
			jQuery.fn.dialog.closeTop();
		} else if (node.attr('sitemap-select-mode') == 'move_copy_delete') {
			var destCID = node.attr('cID');
			var origCID = node.attr('selected-page-id');
			selectMoveCopyTarget(node.attr('sitemap-instance-id'), node.attr('sitemap-display-mode'), node.attr('sitemap-select-mode'), destCID, origCID);
		} else {
			*/


		
	});

}