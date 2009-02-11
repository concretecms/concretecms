
var ccm_totalAdvancedSearchFields = 0;

ccm_activateFileManager = function() {
	//delegate event handling to table container so clicks
	//to our star don't interfer with clicks to our rows
	ccm_alSetupSelectFiles();
	
	$(document).click(function(e) {		
		e.stopPropagation();
		ccm_alSelectNone();
	});
	$(".dialog-launch").dialog();
	
	$("#ccm-file-search-add-option").click(function() {
		ccm_totalAdvancedSearchFields++;
		$("#ccm-file-search-fields-wrapper").append('<div class="ccm-file-search-field" id="ccm-file-search-field-set' + ccm_totalAdvancedSearchFields + '">' + $("#ccm-file-search-field-base").html() + '<\/div>');
		ccm_activateFileManagerFields(ccm_totalAdvancedSearchFields);
	});
	$("#ccm-dashboard-file-search").ajaxForm({
		beforeSubmit: function() {
			ccm_deactivateSearchResults();
		},
			/*beforeSubmit: function() {
				ccm_alShowLoader();
				$("#ccm-al-search-results").html('');
				return true;
			},*/
			
			success: function(resp) {
				ccm_alParseSearchResponse(resp);
			}
	});
	ccm_alSetupInPagePaginationAndSorting();
}

ccm_alParseSearchResponse = function(resp) {
	$("#ccm-file-search-advanced-results").html(resp);
	ccm_activateSearchResults();
	ccm_alSetupSelectFiles();
}

ccm_alDeleteFiles = function() {
	$("#ccm-delete-files-form").ajaxSubmit(function(resp) {
		parseJSON(resp, function() {	
			jQuery.fn.dialog.closeTop();
			ccm_deactivateSearchResults();
			$("#ccm-dashboard-file-search").ajaxSubmit(function(resp) {
				ccm_alParseSearchResponse(resp);
			});
		});
	});
}

ccm_alSetupSelectFiles = function() {
	$('#ccm-file-list').click(function(e){
		e.stopPropagation();
		if ($(e.target).is('img.ccm-star')) {	
			var fID = $(e.target).parents('tr.ccm-file-list-record')[0].id;
			fID = fID.substring(3);
			ccm_starFile(e.target,fID);
		}
		else{
			$(e.target).parents('tr.ccm-file-list-record').each(function(){
				ccm_selectFile($(this), e);		
			});
		}
	});
	$("div.ccm-file-list-thumbnail-image img").hover(function(e) {
		var fID = $(this).parent().attr('fID');
		var obj = $('#fID' + fID + 'hoverThumbnail');
		if (obj.length > 0) {
			var tdiv = obj.find('div');
			var pos = $(this).position();
			tdiv.css('top', pos.top + $(this).attr('height') + 10);
			tdiv.css('left', pos.left);
			tdiv.show();

		}
	}, function() {
		var fID = $(this).parent().attr('fID');
		var obj = $('#fID' + fID + 'hoverThumbnail');
		var tdiv = obj.find('div');
		tdiv.hide();
			
	});
}
ccm_deactivateSearchResults = function() {
	$("#ccm-file-search-advanced-results").css('opacity','0.5');	
	$("#ccm-search-files").attr('disabled', true);
	$("#ccm-file-search-advanced-loading").show();
}

ccm_activateSearchResults = function() {
	$("#ccm-file-search-advanced-results").css('opacity','1');	
	$("#ccm-file-search-advanced-loading").hide();
	$("#ccm-search-files").attr('disabled', false);
	ccm_alSetupInPagePaginationAndSorting();
}

ccm_alSetupInPagePaginationAndSorting = function() {
	$("#ccm-file-list th a").click(function() {
		ccm_deactivateSearchResults();
		$("#ccm-file-search-advanced-results").load($(this).attr('href'), false, function() {
			ccm_activateSearchResults();
			ccm_alSetupSelectFiles();
		});
		return false;
	});
	$("div.ccm-pagination a").click(function() {
		ccm_deactivateSearchResults();
		$("#ccm-file-search-advanced-results").load($(this).attr('href'), false, function() {
			ccm_activateSearchResults();
			ccm_alSetupSelectFiles();
		});
		return false;
	});
}

ccm_activateFileManagerFields = function(fieldset) {
	$("#ccm-file-search-field-set" + fieldset + " select[name=fvField]").unbind();
	$("#ccm-file-search-field-set" + fieldset + " select[name=fvField]").change(function() {
		var selected = $(this).find(':selected').val();
		$(this).next('input.ccm-file-selected-field').val(selected);
		$(this).parents('table').find('.ccm-file-search-option').hide();
		var itemToCopy = $('#ccm-file-search-field-base-elements .ccm-file-search-option[search-field=' + selected + ']');
		$("#ccm-file-search-field-set" + fieldset + " .ccm-file-selected-field-content").html('');
		itemToCopy.clone().appendTo("#ccm-file-search-field-set" + fieldset + " .ccm-file-selected-field-content");

		$("#ccm-file-search-field-set" + fieldset + " .ccm-file-search-option[search-field=date_added] input").each(function() {
			if ($(this).attr('id') == 'date_from') {
				$(this).attr('id', 'date_from' + fieldset);
			} else if ($(this).attr('id') == 'date_to') {
				$(this).attr('id', 'date_to' + fieldset);
			}
		});
	
		$("#ccm-file-search-field-set" + fieldset + " .ccm-file-search-option-type-date input").each(function() {
			$(this).attr('id', $(this).attr('id') + fieldset);
		});
		
		$("#ccm-file-search-field-set" + fieldset + " .ccm-file-search-option[search-field=date_added] input").datepicker({
			showAnim: 'fadeIn'
		});
		$("#ccm-file-search-field-set" + fieldset + " .ccm-file-search-option-type-date input").datepicker({
			showAnim: 'fadeIn'
		});
		
	});
	
	// add the initial state of the latest select menu
	var lastSelect = $("#ccm-file-search-field-set" + fieldset + " select[name=fvField]").eq($(".ccm-file-search-field select[name=fvField]").length-1);
	var selected = lastSelect.find(':selected').val();
	lastSelect.next('input.ccm-file-selected-field').val(selected);

	
	$("#ccm-file-search-field-set" + fieldset + " .ccm-file-search-remove-option").unbind();
	$("#ccm-file-search-field-set" + fieldset + " .ccm-file-search-remove-option").click(function() {
		$(this).parents('table').parent().remove();
		//ccm_totalAdvancedSearchFields--;
	});
}

ccm_alActiveEditableProperties = function() {
	$("tr.ccm-file-manager-editable-field").each(function() {
		var trow = $(this);
		$(this).find('a').click(function() {
			trow.find('.ccm-file-manager-editable-field-text').hide();
			trow.find('.ccm-file-manager-editable-field-form').show();
			trow.find('.ccm-file-manager-editable-field-save-button').show();
		});
		
		trow.find('form').submit(function() {
			ccm_alSubmitEditableProperty(trow);
			return false;
		});
		
		trow.find('.ccm-file-manager-editable-field-save a').click(function() {
			ccm_alSubmitEditableProperty(trow);
		});
	});
}

ccm_alSubmitEditableProperty = function(trow) {
	trow.find('.ccm-file-manager-editable-field-save-button').hide();
	trow.find('.ccm-file-manager-editable-field-loading').show();
	trow.find('form').ajaxSubmit(function(resp) {
		// resp is new HTML to display in the div
		trow.find('.ccm-file-manager-editable-field-loading').hide();
		trow.find('.ccm-file-manager-editable-field-save-button').show();
		trow.find('.ccm-file-manager-editable-field-text').html(resp);
		trow.find('.ccm-file-manager-editable-field-form').hide();
		trow.find('.ccm-file-manager-editable-field-save-button').hide();
		trow.find('.ccm-file-manager-editable-field-text').show();
		trow.find('td').show('highlight', {
			color: '#FFF9BB'
		});

	});
}

ccm_alSubmitSingle = function() {
	if ($("#ccm-al-upload-single-file").val() == '') { 
		alert(ccmi18n_filemanager.uploadErrorChooseFile);
		return false;
	} else {
		$('#ccm-al-upload-single-submit').hide();
		$('#ccm-al-upload-single-loader').show();
	}
}

ccm_alResetSingle = function () {
	$('#ccm-al-upload-single-file').val('');
	$('#ccm-al-upload-single-loader').hide();
	$('#ccm-al-upload-single-submit').show();
}

ccm_alRefresh = function(highlightFIDs) {
	ccm_deactivateSearchResults();
	$("#ccm-file-search-advanced-results").load(CCM_TOOLS_PATH + '/files/search_results', {
		'ccm_order_by': 'fvDateAdded',
		'ccm_order_dir': 'desc'
	}, function() {
		ccm_activateSearchResults();
		ccm_alResetSingle();
		ccm_highlightFileIDArray(highlightFIDs);
		ccm_alSetupSelectFiles();

	});
}

ccm_highlightFileIDArray = function(ids) {
	for (i = 0; i < ids.length; i++) {
		var oldBG = $("#fID" + ids[i] + ' td').css('backgroundColor');
		$("#fID" + ids[i] + ' td').animate({ backgroundColor: '#FFF9BB'}, { queue: true, duration: 300 }).animate( {backgroundColor: oldBG}, 500);
	}
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

ccm_starFile = function (img,fID) {				
	var action = '';
	if ($(img).attr('src').indexOf(CCM_STAR_STATES.unstarred) != -1) {
		$(img).attr('src',$(img).attr('src').replace(CCM_STAR_STATES.unstarred,CCM_STAR_STATES.starred));
		action = 'star';
	}
	else {
		$(img).attr('src',$(img).attr('src').replace(CCM_STAR_STATES.starred,CCM_STAR_STATES.unstarred));
		action = 'unstar';
	}
	
	$.post(CCM_TOOLS_PATH + '/' + CCM_STAR_ACTION,{'action':action,'file-id':fID},function(data, textStatus){
		//callback, in case we want to do some post processing
	});
}