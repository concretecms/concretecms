
var ccm_totalAdvancedSearchFields = 0;
var ccm_alLaunchType = new Array();
var ccm_alActiveAssetField = "";
var ccm_alProcessorTarget = "";
var ccm_alDebug = false;

ccm_triggerSelectFile = function(fID, af) {
	if (af == null) {
		var af = ccm_alActiveAssetField;
	}
	//alert(af);
	var obj = $('#' + af + "-fm-selected");
	var dobj = $('#' + af + "-fm-display");
	dobj.hide();
	obj.show();
	obj.load(CCM_TOOLS_PATH + '/files/selector_data?fID=' + fID + '&ccm_file_selected_field=' + af, function() {
		/*
		$(this).find('a.ccm-file-manager-clear-asset').click(function(e) {
			var field = $(this).attr('ccm-file-manager-field');
			ccm_clearFile(e, field);
		});
		*/
		obj.attr('fID', fID);
		obj.attr('ccm-file-manager-can-view', obj.children('div').attr('ccm-file-manager-can-view'));
		obj.attr('ccm-file-manager-can-edit', obj.children('div').attr('ccm-file-manager-can-edit'));
		obj.attr('ccm-file-manager-can-admin', obj.children('div').attr('ccm-file-manager-can-admin'));
		obj.attr('ccm-file-manager-can-replace', obj.children('div').attr('ccm-file-manager-can-replace'));
		obj.attr('ccm-file-manager-instance', af);
		
		obj.click(function(e) {
			e.stopPropagation();
			ccm_alActivateMenu($(this),e);
		});
		
		if (typeof(ccm_triggerSelectFileComplete)  == 'function') {
			ccm_triggerSelectFileComplete(fID, af);
		}
	});
	var vobj = $('#' + af + "-fm-value");
	vobj.attr('value', fID);
	ccm_alSetupFileProcessor();
}

ccm_alGetFileData = function(fID, onComplete) {
	$.getJSON(CCM_TOOLS_PATH + '/files/get_data.php?fID=' + fID, function(resp) {
		onComplete(resp);
	});
}

ccm_clearFile = function(e, af) {
	e.stopPropagation();
	var obj = $('#' + af + "-fm-selected");
	var dobj = $('#' + af + "-fm-display");
	var vobj = $('#' + af + "-fm-value");
	vobj.attr('value', 0);
	obj.hide();
	dobj.show();
}

ccm_activateFileManager = function(altype, searchInstance) {
	//delegate event handling to table container so clicks
	//to our star don't interfer with clicks to our rows
	ccm_alLaunchType[searchInstance] = altype;
	ccm_alSetupSelectFiles(searchInstance);
	
	$(document).click(function(e) {		
		e.stopPropagation();
		ccm_alSelectNone();
	});

	ccm_setupAdvancedSearch(searchInstance);
	
	if (altype == 'DASHBOARD') {
		$(".dialog-launch").dialog();
	}
	
	
	ccm_alSetupCheckboxes(searchInstance);
	ccm_alSetupFileProcessor();
	ccm_alSetupSingleUploadForm();
	
	$("form#ccm-" + searchInstance + "-advanced-search select[name=fssID]").change(function() {
		if (altype == 'DASHBOARD') { 
			window.location.href = CCM_DISPATCHER_FILENAME + '/dashboard/files/search?fssID=' + $(this).val();
		} else {
			jQuery.fn.dialog.showLoader();
			var url = $("div#ccm-" + searchInstance + "-overlay-wrapper input[name=dialogAction]").val() + "&refreshDialog=1&fssID=" + $(this).val();
			$.get(url, function(resp) {
				jQuery.fn.dialog.hideLoader();
				$("div#ccm-" + searchInstance + "-overlay-wrapper").html(resp);
				$("div#ccm-" + searchInstance + "-overlay-wrapper a.dialog-launch").dialog();
			});
		}
	});

	ccm_searchActivatePostFunction[searchInstance] = function() {
		ccm_alSetupCheckboxes(searchInstance);
		ccm_alSetupSelectFiles(searchInstance);
		ccm_alSetupSingleUploadForm();
	}
	
	
	// setup upload form
}

ccm_alSetupSingleUploadForm = function() {
	$(".ccm-file-manager-submit-single").submit(function() {  
		$(this).attr('target', ccm_alProcessorTarget);
		ccm_alSubmitSingle($(this).get(0));	 
	});
}

ccm_activateFileSelectors = function() {
	$(".ccm-file-manager-launch").unbind();
	$(".ccm-file-manager-launch").click(function() {
		ccm_alLaunchSelectorFileManager($(this).parent().attr('ccm-file-manager-field'));	
	});
}

ccm_alLaunchSelectorFileManager = function(selector) {
	ccm_alActiveAssetField = selector;
	var filterStr = "";
	
	var types = $('#' + selector + '-fm-display input.ccm-file-manager-filter');
	if (types.length) {
		for (i = 0; i < types.length; i++) {
			filterStr += '&' + $(types[i]).attr('name') + '=' + $(types[i]).attr('value');		
		}
	}
	
	ccm_launchFileManager(filterStr);
}

// public method - do not remove or rename
ccm_launchFileManager = function(filters) {
	$.fn.dialog.open({
		width: '90%',
		height: '70%',
		appendButtons: true,
		modal: false,
		href: CCM_TOOLS_PATH + "/files/search_dialog?ocID=" + CCM_CID + "&search=1" + filters,
		title: ccmi18n_filemanager.title
	});
}

ccm_launchFileSetPicker = function(fsID) {
	$.fn.dialog.open({
		width: 500,
		height: 160,
		modal: false,
		href: CCM_TOOLS_PATH + '/files/pick_set?oldFSID=' + fsID,
		title: ccmi18n_filemanager.sets				
	});
}

ccm_alSubmitSetsForm = function(searchInstance) {
	ccm_deactivateSearchResults(searchInstance);
	jQuery.fn.dialog.showLoader();
	$("#ccm-" + searchInstance + "-add-to-set-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		jQuery.fn.dialog.hideLoader();		
		$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
			$("#ccm-" + searchInstance + "-sets-search-wrapper").load(CCM_TOOLS_PATH + '/files/search_sets_reload', {'searchInstance': searchInstance}, function() {
				$(".chosen-select").chosen();
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
			});
		});
	});
}

ccm_alSubmitPasswordForm = function(searchInstance) {
	ccm_deactivateSearchResults(searchInstance);
	$("#ccm-" + searchInstance + "-password-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp, searchInstance);
		});
	});
}

ccm_alSubmitStorageForm = function(searchInstance) {
	ccm_deactivateSearchResults(searchInstance);
	$("#ccm-" + searchInstance + "-storage-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp, searchInstance);
		});
	});
}

ccm_alSubmitPermissionsForm = function(searchInstance) {
	ccm_deactivateSearchResults(searchInstance);
	$("#ccm-" + searchInstance + "-permissions-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp, searchInstance);
		});
	});
}

		
ccm_alSetupSetsForm = function(searchInstance) {
	// activate file set search
	$('#fsAddToSearchName').liveUpdate('ccm-file-search-add-to-sets-list', 'fileset');

	// Setup the tri-state checkboxes
	$('.ccm-file-set-add-cb a').each(function() {
		var cb = $(this);
		var startingState = cb.attr("ccm-tri-state-startup");
		$(this).click(function() {
			var selectedState = $(this).attr("ccm-tri-state-selected");
			var toSetState = 0;
			switch(selectedState) {
				case '0':
					if (startingState == '1') {
						toSetState = '1';
					} else {
						toSetState = '2';
					}
					break;
				case '1':
					toSetState = '2';
					break;
				case '2':
					toSetState = '0';
					break;
			}
			
			$(this).attr('ccm-tri-state-selected', toSetState);
			$(this).parent().find('input').val(toSetState);
			$(this).find('img').attr('src', CCM_IMAGE_PATH + '/checkbox_state_' + toSetState + '.png');
		});
	});
	$("#ccm-" + searchInstance + "-add-to-set-form input[name=fsNew]").click(function() {
		if (!$(this).prop('checked')) {
			$("#ccm-" + searchInstance + "-add-to-set-form input[name=fsNewText]").val('');
		}
	});
	$("#ccm-" + searchInstance + "-add-to-set-form").submit(function() {
		ccm_alSubmitSetsForm(searchInstance);
		return false;
	});
}

ccm_alSetupPasswordForm = function() {
	$("#ccm-file-password-form").submit(function() {
		ccm_alSubmitPasswordForm();
		return false;
	});
}	
ccm_alRescanFiles = function() {
	var turl = CCM_TOOLS_PATH + '/files/rescan?';
	var files = arguments;
	for (i = 0; i < files.length; i++) {
		turl += 'fID[]=' + files[i] + '&';
	}
	$.fn.dialog.open({
		title: ccmi18n_filemanager.rescan,
		href: turl,
		width: 350,
		modal: false,
		height: 200,
		onClose: function() {
			if (files.length == 1) {
				$('#ccm-file-properties-wrapper').html('');
				jQuery.fn.dialog.showLoader();
				
				// open the properties window for this bad boy.
				$("#ccm-file-properties-wrapper").load(CCM_TOOLS_PATH + '/files/properties?fID=' + files[0] + '&reload=1', false, function() {
					jQuery.fn.dialog.hideLoader();
					$(this).find(".dialog-launch").dialog();

				});				
			}
		}
	});
}

	
ccm_alSelectPermissionsEntity = function(selector, id, name) {
	var html = $('#ccm-file-permissions-entity-base').html();
	$('#ccm-file-permissions-entities-wrapper').append('<div class="ccm-file-permissions-entity">' + html + '<\/div>');
	var p = $('.ccm-file-permissions-entity');
	var ap = p[p.length - 1];
	$(ap).find('h3 span').html(name);
	$(ap).find('input[type=hidden]').val(selector + '_' + id);
	$(ap).find('select').each(function() {
		$(this).attr('name', $(this).attr('name') + '_' + selector + '_' + id);
	});
	$(ap).find('div.ccm-file-access-extensions input[type=checkbox]').each(function() {
		$(this).attr('name', $(this).attr('name') + '_' + selector + '_' + id + '[]');
	});
	
	ccm_alActivateFilePermissionsSelector();	
}

ccm_alActivateFilePermissionsSelector = function() {
	$(".ccm-file-access-add select").unbind();
	$(".ccm-file-access-add select").change(function() {
		var p = $(this).parents('div.ccm-file-permissions-entity')[0];
		if ($(this).val() == ccmi18n_filemanager.PTYPE_CUSTOM) {
			$(p).find('div.ccm-file-access-add-extensions').show();				
		} else {
			$(p).find('div.ccm-file-access-add-extensions').hide();				
		}
	});
	$(".ccm-file-access-file-manager select").change(function() {
		var p = $(this).parents('div.ccm-file-permissions-entity')[0];
		if ($(this).val() != ccmi18n_filemanager.PTYPE_NONE) {
			$(p).find('.ccm-file-access-add').show();				
			$(p).find('.ccm-file-access-edit').show();				
			$(p).find('.ccm-file-access-admin').show();
			//$(p).find('div.ccm-file-access-add-extensions').show();				
		} else {
			$(p).find('.ccm-file-access-add').hide();				
			$(p).find('.ccm-file-access-edit').hide();				
			$(p).find('.ccm-file-access-admin').hide();				
			$(p).find('div.ccm-file-access-add-extensions').hide();				
		}
	});


	$("a.ccm-file-permissions-remove").click(function() {
		$(this).parent().parent().fadeOut(100, function() {
			$(this).remove();
		});
	});
	$("input[name=toggleCanAddExtension]").unbind();
	$("input[name=toggleCanAddExtension]").click(function() {
		var ext = $(this).parent().parent().find('div.ccm-file-access-extensions');
		
		if ($(this).prop('checked') == 1) {
			ext.find('input').attr('checked', true);
		} else {
			ext.find('input').attr('checked', false);
		}
	});
}

ccm_alSetupVersionSelector = function() {
	$("#ccm-file-versions-grid input[type=radio]").click(function() {
		$('#ccm-file-versions-grid tr').removeClass('ccm-file-versions-grid-active');
		
		var trow = $(this).parent().parent();
		var fID = trow.attr('fID');
		var fvID = trow.attr('fvID');
		var postStr = 'task=approve_version&fID=' + fID + '&fvID=' + fvID;
		$.post(CCM_TOOLS_PATH + '/files/properties', postStr, function(resp) {
			trow.addClass('ccm-file-versions-grid-active');
			trow.find('td').show('highlight', {
				color: '#FFF9BB'
			});
		});
	});
	
	$(".ccm-file-versions-remove").click(function() {
		var trow = $(this).parent().parent();
		var fID = trow.attr('fID');
		var fvID = trow.attr('fvID');
		var postStr = 'task=delete_version&fID=' + fID + '&fvID=' + fvID;
		$.post(CCM_TOOLS_PATH + '/files/properties', postStr, function(resp) {
			trow.fadeOut(200, function() {
				trow.remove();
			});
		});
		return false;
	});
}

ccm_alDeleteFiles = function(searchInstance) {
	$("#ccm-" + searchInstance + "-delete-form").ajaxSubmit(function(resp) {
		ccm_parseJSON(resp, function() {	
			jQuery.fn.dialog.closeTop();
			ccm_deactivateSearchResults(searchInstance);
			$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
			});
		});
	});
}

ccm_alDuplicateFiles = function(searchInstance) {
	$("#ccm-" + searchInstance + "-duplicate-form").ajaxSubmit(function(resp) {
		ccm_parseJSON(resp, function() {	
			jQuery.fn.dialog.closeTop();
			ccm_deactivateSearchResults(searchInstance);
			var r = eval('(' + resp + ')');

			$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
				var highlight = new Array();
				for (i = 0; i < r.fID.length; i++ ){
					fID = r.fID[i];
					ccm_uploadedFiles.push(fID);
					highlight.push(fID);
				}
				ccm_alRefresh(highlight, searchInstance);
				ccm_filesUploadedDialog(searchInstance);				
			});
		});
	});
}

ccm_alSetupSelectFiles = function(searchInstance) {
	$('.ccm-file-list').unbind();
	/*
	$('.ccm-file-list').click(function(e){
		e.stopPropagation();
		if ($(e.target).is('img.ccm-star')) {	
			var fID = $(e.target).parents('tr.ccm-list-record')[0].id;
			fID = fID.substring(3);
			ccm_starFile(e.target,fID);
		}
		else{
			$(e.target).parents('tr.ccm-list-record').each(function(){
				ccm_alActivateMenu($(this), e);		
			});
		}
	});
	*/
	
	$('.ccm-file-list tr.ccm-list-record').click(function(e) {
		e.stopPropagation();
		ccm_alActivateMenu($(this), e);
	});
	$('.ccm-file-list img.ccm-star').click(function(e) {
		e.stopPropagation();
		var fID = $(e.target).parents('tr.ccm-list-record')[0].id;
		fID = fID.substring(3);
		ccm_starFile(e.target,fID);
	});
	if (ccm_alLaunchType[searchInstance] == 'DASHBOARD') {
		$(".ccm-file-list-thumbnail").hover(function(e) { 
			var fID = $(this).attr('fID');
			var obj = $('#fID' + fID + 'hoverThumbnail'); 
			if (obj.length > 0) { 
				var tdiv = obj.find('div');
				var pos = obj.position();
				tdiv.css('top', pos.top);
				tdiv.css('left', pos.left);
				tdiv.show();
			}
		}, function() {
			var fID = $(this).attr('fID');
			var obj = $('#fID' + fID + 'hoverThumbnail');
			var tdiv = obj.find('div');
			tdiv.hide(); 
		});
	}
}

ccm_alSetupCheckboxes = function(searchInstance) {
	$("#ccm-" + searchInstance + "-list-cb-all").unbind();	
	$("#ccm-" + searchInstance + "-list-cb-all").click(function() {
		ccm_hideMenus();
		if ($(this).prop('checked') == true) {
			$('#ccm-' + searchInstance + '-search-results td.ccm-file-list-cb input[type=checkbox]').attr('checked', true);
			$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', false);
		} else {
			$('#ccm-' + searchInstance + '-search-results td.ccm-file-list-cb input[type=checkbox]').attr('checked', false);
			$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', true);
		}
	});
	$("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb input[type=checkbox]").click(function(e) {
		e.stopPropagation();
		ccm_hideMenus();
		ccm_alRescanMultiFileMenu(searchInstance);
	});
	$("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb").click(function(e) {
		e.stopPropagation();
		ccm_hideMenus();
		$(this).find('input[type=checkbox]').click();
		ccm_alRescanMultiFileMenu(searchInstance);
	});
	
	// if we're not in the dashboard, add to the multiple operations select menu
	if (ccm_alLaunchType[searchInstance] != 'DASHBOARD' && ccm_alLaunchType[searchInstance] != 'BROWSE') {
		var chooseText = ccmi18n_filemanager.select;
		$("#ccm-" + searchInstance + "-list-multiple-operations option:eq(0)").after("<option value=\"choose\">" + chooseText + "</option>");
	}
	$("#ccm-" + searchInstance + "-list-multiple-operations").change(function() {
		var action = $(this).val();
		var fIDstring = ccm_alGetSelectedFileIDs(searchInstance);
		switch(action) {
			case 'choose':
				var fIDs = new Array();
				$("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb input[type=checkbox]:checked").each(function() {
					fIDs.push($(this).val());
				});
				ccm_alSelectFile(fIDs, true);
				break;
			case "delete":
				jQuery.fn.dialog.open({
					width: 500,
					height: 400,
					modal: false,
					appendButtons: true,
					href: CCM_TOOLS_PATH + '/files/delete?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n_filemanager.deleteFile				
				});
				break;
			case "duplicate":
				jQuery.fn.dialog.open({
					width: 500,
					height: 400,
					modal: false,
					href: CCM_TOOLS_PATH + '/files/duplicate?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n_filemanager.duplicateFile				
				});
				break;
			case "sets":
				jQuery.fn.dialog.open({
					width: 500,
					height: 400,
					modal: false,
					href: CCM_TOOLS_PATH + '/files/add_to?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n_filemanager.sets				
				});
				break;
			case "properties": 
				jQuery.fn.dialog.open({
					width: 690,
					height: 440,
					modal: false,
					href: CCM_TOOLS_PATH + '/files/bulk_properties?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n.properties				
				});
				break;				
			case "rescan":
				jQuery.fn.dialog.open({
					width: 350,
					height: 200,
					modal: false,
					href: CCM_TOOLS_PATH + '/files/rescan?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n_filemanager.rescan,
					onClose: function() {
						$("#ccm-" + searchInstance + "-advanced-search").submit();			
					}
				});
				break;
			case "download":
				window.frames[ccm_alProcessorTarget].location = CCM_TOOLS_PATH + '/files/download?' + fIDstring;
				break;
		}
		
		$(this).get(0).selectedIndex = 0;
	});

	// activate the file sets checkboxes
	ccm_alSetupFileSetSearch(searchInstance);
}

ccm_alSetupFileSetSearch = function(searchInstance) {
	$("#ccm-" + searchInstance + "-sets-search-wrapper select").chosen().unbind();
	$("#ccm-" + searchInstance + "-sets-search-wrapper select").chosen().change(function() {
		var sel = $("#ccm-" + searchInstance + "-sets-search-wrapper option:selected");
		$("#ccm-" + searchInstance + "-advanced-search").submit();
	});

	/*
	$(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").unbind();
	$(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").click(function() {
		$("input[name=fsIDNone][instance=" + searchInstance + "]").attr('checked', false);
		$("#ccm-" + searchInstance + "-advanced-search").submit();
	});
	
	// activate file set search
	$('div.ccm-file-sets-search-wrapper-input input').liveUpdate('ccm-file-search-advanced-sets-list', 'fileset');
	
	$("input[name=fsIDNone][instance=" + searchInstance + "]").unbind();
	$("input[name=fsIDNone][instance=" + searchInstance + "]").click(function() {
		if ($(this).prop('checked')) {
			$(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").attr('checked', false);
			$(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").attr('disabled', true);
		} else {
			$(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").attr('disabled', false);
		}
		$("#ccm-" + searchInstance + "-advanced-search").submit();
	});
	*/
}


ccm_alGetSelectedFileIDs = function(searchInstance) {
	var fidstr = '';
	$("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb input[type=checkbox]:checked").each(function() {
		fidstr += 'fID[]=' + $(this).val() + '&';
	});
	return fidstr;
}

ccm_alRescanMultiFileMenu = function(searchInstance) {
	if ($("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb input[type=checkbox]:checked").length > 0) {
		$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', false);
	} else {
		$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', true);
	}
}

ccm_alSetupFileProcessor = function() {
	if (ccm_alProcessorTarget != '') {
		return false;
	}
	
	var ts = parseInt(new Date().getTime().toString().substring(0, 10)); 
	var ifr; 
	try { //IE7 hack
	  ifr = document.createElement('<iframe name="ccm-al-upload-processor'+ts+'">');
	} catch (ex) {
	  ifr = document.createElement('iframe');
	}	
	ifr.id = 'ccm-al-upload-processor' + ts;
	ifr.name = 'ccm-al-upload-processor' + ts;
	ifr.style.border='0px';
	ifr.style.width='0px';
	ifr.style.height='0px';
	ifr.style.display = "none";
	document.body.appendChild(ifr);
	
	if (ccm_alDebug) {
		ccm_alProcessorTarget = "_blank";
	} else {
		ccm_alProcessorTarget = 'ccm-al-upload-processor' + ts;
	}
}

ccm_alSubmitSingle = function(form) {
	if ($(form).find(".ccm-al-upload-single-file").val() == '') { 
		return false;
	} else { 
		$(form).find('.ccm-al-upload-single-submit').hide();
		$(form).find('.ccm-al-upload-single-loader').show();
	}
}

ccm_alResetSingle = function () {
	$('.ccm-al-upload-single-file').val('');
	$('.ccm-al-upload-single-loader').hide();
	$('.ccm-al-upload-single-submit').show();
}

var ccm_uploadedFiles=[];
ccm_filesUploadedDialog = function(searchInstance) { 
	if(document.getElementById('ccm-file-upload-multiple-tab')) 
		jQuery.fn.dialog.closeTop()
	var fIDstring='';
	for( var i=0; i< ccm_uploadedFiles.length; i++ )
		fIDstring=fIDstring+'&fID[]='+ccm_uploadedFiles[i];
	jQuery.fn.dialog.open({
		width: 690,
		height: 440,
		modal: false,
		href: CCM_TOOLS_PATH + '/files/bulk_properties/?'+fIDstring + '&uploaded=true&searchInstance=' + searchInstance,
		onClose: function() {
			ccm_deactivateSearchResults(searchInstance);
			$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
			});
		},
		title: ccmi18n_filemanager.uploadComplete				
	});
	ccm_uploadedFiles=[];
}

ccm_alSetupUploadDetailsForm = function(searchInstance) {
	$("#ccm-" + searchInstance + "-update-uploaded-details-form").submit(function() {
		ccm_alSubmitUploadDetailsForm(searchInstance);
		return false;
	});
}

ccm_alSubmitUploadDetailsForm = function(searchInstance) {
	jQuery.fn.dialog.showLoader();
	$("#ccm-" + searchInstance + "-update-uploaded-details-form").ajaxSubmit(function(r1) {
		var r1a = eval('(' + r1 + ')');
		var form = $("#ccm-" + searchInstance + "-advanced-search");
		if (form.length > 0) {
			form.ajaxSubmit(function(resp) {
				$("#ccm-" + searchInstance + "-sets-search-wrapper").load(CCM_TOOLS_PATH + '/files/search_sets_reload', {'searchInstance': searchInstance}, function() {
					jQuery.fn.dialog.hideLoader();
					jQuery.fn.dialog.closeTop();
					ccm_parseAdvancedSearchResponse(resp, searchInstance);
					ccm_alHighlightFileIDArray(r1a);
				});
			});
		} else {
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
		}
	});
}

ccm_alRefresh = function(highlightFIDs, searchInstance, fileSelector) {
	var ids = highlightFIDs;
	ccm_deactivateSearchResults(searchInstance);
	$("#ccm-" + searchInstance + "-search-results").load(CCM_TOOLS_PATH + '/files/search_results', {
		'ccm_order_by': 'fvDateAdded',
		'ccm_order_dir': 'desc', 
		'fileSelector': fileSelector,
		'searchType' : ccm_alLaunchType[searchInstance],
		'searchInstance': searchInstance
	}, function() {
		ccm_activateSearchResults(searchInstance);
		if (ids != false) {
			ccm_alHighlightFileIDArray(ids);
		}
		ccm_alSetupSelectFiles(searchInstance);

	});
}

ccm_alHighlightFileIDArray = function(ids) {
	for (i = 0; i < ids.length; i++) {
		var td = $('tr[fID=' + ids[i] + '] td');
		var oldBG = td.css('backgroundColor');
		td.animate({ backgroundColor: '#FFF9BB'}, { queue: true, duration: 1000 }).animate( {backgroundColor: oldBG}, 500);
	}
}

ccm_alSelectFile = function(fID) {
	
	if (typeof(ccm_chooseAsset) == 'function') {
		var qstring = '';
		if (typeof(fID) == 'object') {
			for (i = 0; i < fID.length; i++) {
				qstring += 'fID[]=' + fID[i] + '&';
			}
		} else {
			qstring += 'fID=' + fID;
		}
		
		$.getJSON(CCM_TOOLS_PATH + '/files/get_data.php?' + qstring, function(resp) {
			ccm_parseJSON(resp, function() {
				for(i = 0; i < resp.length; i++) {
					ccm_chooseAsset(resp[i]);
				}
				jQuery.fn.dialog.closeTop();
			});
		});
		
	} else {
		if (typeof(fID) == 'object') {
			for (i = 0; i < fID.length; i++) {
				ccm_triggerSelectFile(fID[i]);
			}
		} else {
			ccm_triggerSelectFile(fID);
		}
		jQuery.fn.dialog.closeTop();	
	}

}

ccm_alActivateMenu = function(obj, e) {
	
	// Is this a file that's already been chosen that we're selecting?
	// If so, we need to offer the reset switch
	
	var selectedFile = $(obj).find('div[ccm-file-manager-field]');
	var selector = '';
	if(selectedFile.length > 0) {
		selector = selectedFile.attr('ccm-file-manager-field');
	} 
	if (!selector) {
		selector = 	ccm_alActiveAssetField;
	}
	ccm_hideMenus();
	
	var fID = $(obj).attr('fID');
	var searchInstance = $(obj).attr('ccm-file-manager-instance');

	// now, check to see if this menu has been made
	var bobj = document.getElementById("ccm-al-menu" + fID + searchInstance + selector);
	
	// This immediate click mode has promise, but it's annoying more than it's helpful
	/*
	if (ccm_alLaunchType != 'DASHBOARD' && selector == '') {
		// then we are in file list mode in the site, which means we 
		// we don't give out all the options in the list
		ccm_alSelectFile(fID);
		return;
	}
	*/
	
	if (!bobj) {
		// create the 1st instance of the menu
		el = document.createElement("DIV");
		el.id = "ccm-al-menu" + fID + searchInstance + selector;
		el.className = "ccm-menu ccm-ui";
		el.style.display = "block";
		el.style.visibility = "hidden";
		document.body.appendChild(el);
		
		var passedFilters = $('div[ccm-file-manager-field=' + selector + '] input.ccm-file-manager-filter');
		var filterStr = '';
		if (passedFilters.length > 0) {
			passedFilters.each(function() {
				filterStr += '&' + $(this).attr('name') + '=' + $(this).attr('value');
			});
		}
		var filepath = $(obj).attr('al-filepath'); 
		bobj = $("#ccm-al-menu" + fID + searchInstance + selector);
		bobj.css("position", "absolute");
		
		//contents  of menu
		var html = '<div class="popover"><div class="arrow"></div><div class="inner"><div class="content">';
		html += '<ul>';
		if (ccm_alLaunchType[searchInstance] != 'DASHBOARD' && ccm_alLaunchType[searchInstance] != 'BROWSE') {
			// if we're launching this at the selector level, that means we've already chosen a file, and this should instead launch the library
			var onclick = (selectedFile.length > 0) ? 'ccm_alLaunchSelectorFileManager(\'' + selector + '\')' : 'ccm_alSelectFile(' + fID + ')';
			var chooseText = (selectedFile.length > 0) ? ccmi18n_filemanager.chooseNew : ccmi18n_filemanager.select;
			html += '<li><a class="ccm-menu-icon ccm-icon-choose-file-menu" dialog-modal="false" dialog-width="90%" dialog-height="70%" dialog-title="' + ccmi18n_filemanager.select + '" id="menuSelectFile' + fID + '" href="javascript:void(0)" onclick="' + onclick + '">'+ chooseText + '<\/a><\/li>';
		}
		if (selectedFile.length > 0) {
			html += '<li><a class="ccm-menu-icon ccm-icon-clear-file-menu" href="javascript:void(0)" id="menuClearFile' + fID + searchInstance + selector + '">'+ ccmi18n_filemanager.clear + '<\/a><\/li>';
		}
		
		if (ccm_alLaunchType[searchInstance] != 'DASHBOARD'  && ccm_alLaunchType[searchInstance] != 'BROWSE' && selectedFile.length > 0) {
			html += '<li class="ccm-menu-separator"></li>';	
		}
		if ($(obj).attr('ccm-file-manager-can-view') == '1') {
			html += '<li><a class="ccm-menu-icon ccm-icon-view dialog-launch" dialog-modal="false" dialog-append-buttons="true" dialog-width="90%" dialog-height="75%" dialog-title="' + ccmi18n_filemanager.view + '" id="menuView' + fID + '" href="' + CCM_TOOLS_PATH + '/files/view?fID=' + fID + '">'+ ccmi18n_filemanager.view + '<\/a><\/li>';
		} else {
			html += '<li><a class="ccm-menu-icon ccm-icon-download-menu" href="javascript:void(0)" id="menuDownload' + fID + '" onclick="window.frames[\'' + ccm_alProcessorTarget + '\'].location=\'' + CCM_TOOLS_PATH + '/files/download?fID=' + fID + '\'">'+ ccmi18n_filemanager.download + '<\/a><\/li>';	
		}
		if ($(obj).attr('ccm-file-manager-can-edit') == '1') {
			html += '<li><a class="ccm-menu-icon ccm-icon-edit-menu dialog-launch" dialog-modal="false" dialog-width="90%" dialog-height="75%" dialog-title="' + ccmi18n_filemanager.edit + '" id="menuEdit' + fID + '" href="' + CCM_TOOLS_PATH + '/files/edit?searchInstance=' + searchInstance + '&fID=' + fID + filterStr + '">'+ ccmi18n_filemanager.edit + '<\/a><\/li>';
		}
		html += '<li><a class="ccm-menu-icon ccm-icon-properties-menu dialog-launch" dialog-modal="false" dialog-width="680" dialog-height="450" dialog-title="' + ccmi18n_filemanager.properties + '" id="menuProperties' + fID + '" href="' + CCM_TOOLS_PATH + '/files/properties?searchInstance=' + searchInstance + '&fID=' + fID + '">'+ ccmi18n_filemanager.properties + '<\/a><\/li>';
		if ($(obj).attr('ccm-file-manager-can-replace') == '1') {
			html += '<li><a class="ccm-menu-icon ccm-icon-replace dialog-launch" dialog-modal="false" dialog-width="300" dialog-height="260" dialog-title="' + ccmi18n_filemanager.replace + '" id="menuFileReplace' + fID + '" href="' + CCM_TOOLS_PATH + '/files/replace?searchInstance=' + searchInstance + '&fID=' + fID + '">'+ ccmi18n_filemanager.replace + '<\/a><\/li>';
		}
		if ($(obj).attr('ccm-file-manager-can-duplicate') == '1') {
			html += '<li><a class="ccm-menu-icon ccm-icon-copy-menu" id="menuFileDuplicate' + fID + '" href="javascript:void(0)" onclick="ccm_alDuplicateFile(' + fID + ',\'' + searchInstance + '\')">'+ ccmi18n_filemanager.duplicate + '<\/a><\/li>';
		}
		html += '<li><a class="ccm-menu-icon ccm-icon-sets dialog-launch" dialog-modal="false" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.sets + '" id="menuFileSets' + fID + '" href="' + CCM_TOOLS_PATH + '/files/add_to?searchInstance=' + searchInstance + '&fID=' + fID + '">'+ ccmi18n_filemanager.sets + '<\/a><\/li>';
		if ($(obj).attr('ccm-file-manager-can-admin') == '1' || $(obj).attr('ccm-file-manager-can-delete') == '1') {
			html += '<li class="ccm-menu-separator"></li>';
		}
		if ($(obj).attr('ccm-file-manager-can-admin') == '1') {
			html += '<li><a class="ccm-menu-icon ccm-icon-access-permissions dialog-launch" dialog-modal="false" dialog-width="400" dialog-height="450" dialog-title="' + ccmi18n_filemanager.permissions + '" id="menuFilePermissions' + fID + '" href="' + CCM_TOOLS_PATH + '/files/permissions?searchInstance=' + searchInstance + '&fID=' + fID + '">'+ ccmi18n_filemanager.permissions + '<\/a><\/li>';
		}
		if ($(obj).attr('ccm-file-manager-can-delete') == '1') {
			html += '<li><a class="ccm-icon-delete-menu ccm-menu-icon dialog-launch" dialog-append-buttons="true" dialog-modal="false" dialog-width="500" dialog-height="200" dialog-title="' + ccmi18n_filemanager.deleteFile + '" id="menuDeleteFile' + fID + '" href="' + CCM_TOOLS_PATH + '/files/delete?searchInstance=' + searchInstance + '&fID=' + fID + '">'+ ccmi18n_filemanager.deleteFile + '<\/a><\/li>';
		}
		html += '</ul>';
		html += '</div></div></div>';
		bobj.append(html);

		$(bobj).find('a').bind('click.hide-menu', function(e) {
			ccm_hideMenus();
			return false;	
		});
		
		$("#ccm-al-menu" + fID + searchInstance + selector + " a.dialog-launch").dialog();
		
		$('a#menuClearFile' + fID + searchInstance + selector).click(function(e) {
			ccm_clearFile(e, selector);
			ccm_hideMenus();
		});

	} else {
		bobj = $("#ccm-al-menu" + fID + searchInstance + selector);
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

ccm_alDuplicateFile = function(fID, searchInstance) {
	var postStr = 'fID=' + fID + '&searchInstance=' + searchInstance;
	
	$.post(CCM_TOOLS_PATH + '/files/duplicate', postStr, function(resp) {
		var r = eval('(' + resp + ')');
		
		if (r.error == 1) {
		 	ccmAlert.notice(ccmi18n.error, r.message);		
		 	return false;
		 }
		
		
		var highlight = new Array();
		if (r.fID) {
			highlight.push(r.fID);
			ccm_alRefresh(highlight, searchInstance);
			ccm_uploadedFiles.push(r.fID);
			ccm_filesUploadedDialog(searchInstance);
		}
	});
}

ccm_alSelectMultipleIncomingFiles = function(obj) {
	if ($(obj).prop('checked')) {
		$("input.ccm-file-select-incoming").attr('checked', true);
	} else {
		$("input.ccm-file-select-incoming").attr('checked', false);
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
