
ccm_activateFileManager = function() {
	$("tr.ccm-file-list-record").each(function() {
		$(this).click(function(e) {
			e.stopPropagation();
			ccm_selectFile($(this), e);		
		});
	});
	$(document).click(function(e) {
		e.stopPropagation();
		ccm_alSelectNone();
	});
	$(".dialog-launch").dialog();
	
	$("#ccm-file-search-add-option").click(function() {
		$("#ccm-file-search-fields-wrapper").append('<div class="ccm-file-search-field">' + $("#ccm-file-search-field-base").html() + '<\/div>');
		ccm_activateFileManagerFields();
	});
	$("#ccm-dashboard-file-search").ajaxForm({
		beforeSubmit: function() {
			$("#ccm-file-search-advanced-results").css('opacity','0.5');	
			$("#ccm-search-files").attr('disabled', true);
			$("#ccm-file-search-advanced-loading").show();
		},
			/*beforeSubmit: function() {
				ccm_alShowLoader();
				$("#ccm-al-search-results").html('');
				return true;
			},*/
			
			success: function(resp) {
				$("#ccm-file-search-advanced-results").css('opacity','1');	
				$("#ccm-file-search-advanced-loading").hide();
				$("#ccm-search-files").attr('disabled', false);
				$("#ccm-file-search-advanced-results").html(resp);
				//$("#ccm-al-search-results").html(resp);	
			}
	});
}

ccm_activateFileManagerFields = function() {
	$(".ccm-file-search-field select[name=fvField]").unbind();
	$(".ccm-file-search-field select[name=fvField]").change(function() {
		var selected = $(this).find(':selected').val();
		$(this).next('input.ccm-file-selected-field').val(selected);
		$(this).parents('table').find('.ccm-file-search-option').hide();
		$(this).parents('table').find('.ccm-file-search-option[search-field=' + selected + ']').show();		
	});
	$(".ccm-file-search-remove-option").unbind();
	$(".ccm-file-search-remove-option").click(function() {
		$(this).parents('table').parent().remove();
	});
}

ccm_alSubmitSingle = function() {
	$('#ccm-al-upload-single-submit').hide();
	$('#ccm-al-upload-single-loader').show();
}

ccm_alResetSingle = function () {
	$('#ccm-al-upload-single-file').val('');
	$('#ccm-al-upload-single-loader').hide();
	$('#ccm-al-upload-single-submit').show();
}

ccm_alRefresh = function() {
	/*
	$('#fileSearch_bDateAdded').val('');
	$('#fileSearch_bFile').val('');	
	$('#fileSearchSorting').val('bDateAdded desc');
	
	//$("#ccm-al-add-asset").hide();
	//$("#ccm-al").show();
	$('#ccm-al-search-button').get(0).click()
	
	$("#ccm-al-search-results").load('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/al_search_results.php', {
		sort: 'bDateAdded', order: 'desc', view: parseInt($('#search_page_size').val())
	});
	*/
	ccm_alResetSingle();
}

ccm_selectFile = function(obj, e) {
	ccm_hideMenus();
	
	var fID = $(obj).attr('id').substring(3);

	// now, check to see if this menu has been made
	var bobj = document.getElementById("ccm-al-menu" + fID);

	if (!bobj) {
		
		// create the 1st instance of the menu
		el = document.createElement("DIV");
		el.id = "ccm-al-menu" + fID;
		el.className = "ccm-menu";
		el.style.display = "none";
		document.body.appendChild(el);
		
		var filepath = $(obj).attr('al-filepath'); 
		bobj = $("#ccm-al-menu" + fID);
		bobj.css("position", "absolute");
		
		//contents  of menu
		var html = '<div class="ccm-menu-tl"><div class="ccm-menu-tr"><div class="ccm-menu-t"></div></div></div>';
		html += '<div class="ccm-menu-l"><div class="ccm-menu-r">';
		html += '<ul>';
		html += '<li><a class="ccm-icon" dialog-modal="false" dialog-width="90%" dialog-height="70%" dialog-title="' + ccmi18n_filemanager.viewDownload + '" id="menuViewDownload' + fID + '" href="' + CCM_TOOLS_PATH + '/files/view_download?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/window_new.png)">'+ ccmi18n_filemanager.viewDownload + '<\/span><\/a><\/li>';
		html += '<li><a class="ccm-icon" dialog-modal="false" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.properties + '" id="menuProperties' + fID + '" href="' + CCM_TOOLS_PATH + '/files/properties?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/edit_small.png)">'+ ccmi18n_filemanager.properties + '<\/span><\/a><\/li>';
		html += '<li><a class="ccm-icon" dialog-modal="false" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.editReplace + '" id="menuFileEditReplace' + fID + '" href="' + CCM_TOOLS_PATH + '/files/edit?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/window_new.png)">'+ ccmi18n_filemanager.editReplace + '<\/span><\/a><\/li>';
		html += '<li><a class="ccm-icon" dialog-modal="false" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.addTo + '" id="menuFileAddTo' + fID + '" href="' + CCM_TOOLS_PATH + '/files/add_to?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/window_new.png)">'+ ccmi18n_filemanager.addTo + '<\/span><\/a><\/li>';
		html += '<li><a class="ccm-icon" dialog-modal="false" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.permissions + '" id="menuFilePermissions' + fID + '" href="' + CCM_TOOLS_PATH + '/files/permissions?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/window_new.png)">'+ ccmi18n_filemanager.permissions + '<\/span><\/a><\/li>';
		html += '<li><a class="ccm-icon" dialog-modal="false" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.deleteFile + '" id="menuDeleteFile' + fID + '" href="' + CCM_TOOLS_PATH + '/files/delete?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/window_new.png)">'+ ccmi18n_filemanager.deleteFile + '<\/span><\/a><\/li>';
		html += '</ul>';
		html += '</div></div>';
		html += '<div class="ccm-menu-bl"><div class="ccm-menu-br"><div class="ccm-menu-b"></div></div></div>';
		bobj.append(html);
		
		$('a#menuViewDownload' + fID).dialog();
		$('a#menuProperties' + fID).dialog();
		$('a#menuFileEditReplace' + fID).dialog();
		$('a#menuFileAddTo' + fID).dialog();
		$('a#menuFilePermissions' + fID).dialog();
		$('a#menuDeleteFile' + fID).dialog();
		/*		
		$('a#menuProperties' + fID).dialog();
		$('a#menuDelete' + fID).click(function() {
			if (confirm('<?=t('Are you sure you want to delete this file?')?>')) {
				$.getJSON('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/al_delete.php', {'fID': fID, 'ccm_token': '<?=$valt->generate('delete_file')?>'}, function(resp) {
					parseJSON(resp, function() {
						if(resp.error==1) alert(resp.message);
						else{
							$(obj).fadeOut(300);
							//update paging result details
							var ptr=$('#pagingTotalResults');
							var ppr=$('#pagingPageResults');
							ptr.html(  parseInt(ptr.html())-1  );
							if( parseInt(ptr.html())<=parseInt(ppr.html()) ){
								ppr.html(  parseInt(ptr.html())  );
								$('.ccm-al-actions .ccm-paging').css('display','none');
							}
						}
					});
				});
			}
		});
		*/

	} else {
		bobj = $("#ccm-al-menu" + fID);
	}
	
	ccm_fadeInMenu(bobj, e);

}

ccm_alSelectNone = function() {
	ccm_hideMenus();
}

var checkbox_status = false;
toggleCheckboxStatus = function(form) {
	if(checkbox_status) {
		for (i = 0; i < form.elements.length; i++) {
			if (form.elements[i].type == "checkbox") {
				form.elements[i].checked = false;
			}
		}	
		checkbox_status = false;
	}
	else {
		for (i = 0; i < form.elements.length; i++) {
			if (form.elements[i].type == "checkbox") {
				form.elements[i].checked = true;
			}
		}	
		checkbox_status = true;	
	}
}	
