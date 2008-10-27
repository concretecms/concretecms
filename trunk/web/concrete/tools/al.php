<?

defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
if (!$cp->canRead()) {
	die(_("Unable to access the file manager."));
}
Loader::library('search');
Loader::model('search/file');

$ci = Loader::helper('concrete/urls');
$uploadURL = $ci->getToolsURL('al_upload');

if(!isset($_REQUEST['sort'])) $_REQUEST['sort'] = 'bDateAdded desc';
?>

<? if ($_REQUEST['launch_in_page']) {
	$viewType = 'popup';
}
?>

<style type="text/css">
div.ccm-al-image, div.ccm-al-image-selected  {width: <?=AL_THUMBNAIL_WIDTH+4?>px; height: <?=AL_THUMBNAIL_HEIGHT+4?>px}
</style>

<script type="text/javascript">

var ccm_alSelectedItem = false;

ccm_priSelectAssetAuto = function( objId ){ 
	var obj=$('#'+objId);
	var bID = obj.attr('id').substring(7);
	var thumbpath = obj.attr('al-thumb-path');
	var filepath = obj.attr('al-filepath');
	var filename = obj.attr('al-filename');
	var filewidth = obj.attr('al-width');
	var fileheight = obj.attr('al-height');
	var filetype = obj.attr('al-type');
	var origfilename = obj.attr('al-origfilename');
	ccm_priSelectAsset(bID,filepath,thumbpath,filename,filewidth,fileheight,filetype,origfilename);
}

ccm_alSelectItem = function(obj, e) {
	ccm_hideMenus();
	
	var bID = $(obj).attr('id').substring(7);
	$(obj).addClass('ccm-al-image-selected');
	ccm_alSelectedItem = obj;

	// now, check to see if this menu has been made
	var bobj = document.getElementById("ccm-al-menu" + bID);

	if (!bobj) {
		
		// create the 1st instance of the menu
		el = document.createElement("DIV");
		el.id = "ccm-al-menu" + bID;
		el.className = "ccm-menu";
		el.style.display = "none";
		document.body.appendChild(el);
		
		var filepath = $(obj).attr('al-filepath'); 
		bobj = $("#ccm-al-menu" + bID);
		bobj.css("position", "absolute");
		
		//contents  of menu
		var html = '<div class="ccm-menu-tl"><div class="ccm-menu-tr"><div class="ccm-menu-t"></div></div></div>';
		html += '<div class="ccm-menu-l"><div class="ccm-menu-r">';
		html += '<ul>';
		html += '<li><a class="ccm-icon" id="menuVisit' + bID + '" href="' + filepath + '" target="_blank"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/window_new.png)"><?=t('View/Download')?><\/span><\/a><\/li>';
		html += '<li><a class="ccm-icon" dialog-modal="false" dialog-width="350" dialog-height="350" dialog-title="<?=t('File Properties')?>" id="menuProperties' + bID + '" href="<?=REL_DIR_FILES_TOOLS_BLOCKS?>/library_file/properties.php?bID=' + bID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/edit_small.png)"><?=t('Properties')?><\/span><\/a><\/li>';
		<? if ($viewType == 'popup') { ?>
			html += '<li><a id="" class="ccm-icon" href="javascript:void(0)" onclick="ccm_priSelectAssetAuto(\'' + $(obj).attr('id') + '\')"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/add.png)"><?=t('Select')?><\/span><\/a><\/li>';
		<? } else { ?>
			html += '<li><a class="ccm-icon" href="javascript:void(0)" id="menuDelete' + bID +'"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/delete_small.png)"><?=t('Delete File')?><\/span><\/a><\/li>';
		<? } ?>
		html += '</ul>';
		html += '</div></div>';
		html += '<div class="ccm-menu-bl"><div class="ccm-menu-br"><div class="ccm-menu-b"></div></div></div>';
		bobj.append(html);
		
		$('a#menuProperties' + bID).dialog();
		$('a#menuDelete' + bID).click(function() {
			if (confirm('<?=t('Are you sure you want to delete this file?')?>')) {
				$.getJSON('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/al_delete.php', {'bID': bID}, function(resp) {
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

	} else {
		bobj = $("#ccm-al-menu" + bID);
	}
	
	ccm_fadeInMenu(bobj, e);

}

ccm_alSelectNone = function() {
	ccm_hideMenus();
	/*
	if (ccm_alSelectedItem != false) {
		ccm_alDeselectItem(ccm_alSelectedItem);
		ccm_alSelectedItem = false;
		$('#ccm-al-info-detail').fadeOut(200);
	}*/
	
}

ccm_alDeselectItem = function(obj) {
	$(obj).removeClass('ccm-al-image-selected');
}

ccm_priSelectAsset = function(bID, filePath, thumbPath, fileName, width, height, type, origfilename){
	$("#ccm-al-info-detail").hide();
	obj = {'bID': bID, 'filePath': filePath, 'thumbPath': thumbPath, 'fileName': fileName, 'width': width, 'height': height, 'type': type, 'origfilename': origfilename}
	ccm_chooseAsset(obj);
 	jQuery.fn.dialog.closeTop();
}

ccm_alRefresh = function() {
	$('#fileSearch_bDateAdded').val('');
	$('#fileSearch_bFile').val('');	
	$('#fileSearchSorting').val('bDateAdded desc');
	
	//$("#ccm-al-add-asset").hide();
	//$("#ccm-al").show();
	$('#ccm-al-search-button').get(0).click()
	/*
	$("#ccm-al-search-results").load('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/al_search_results.php', {
		sort: 'bDateAdded', order: 'desc', view: parseInt($('#search_page_size').val())
	});
	*/
}

ccm_alShowLoader = function() { 
	//$('#loadingIcon').css('display','block');
	jQuery.fn.dialog.showLoader();
}

ccm_alHideLoader = function() {
	//$('#loadingIcon').css('display','none');
	jQuery.fn.dialog.hideLoader();
}


ccm_alSetupPaging = function() {
	/* setup paging */
	$("div.ccm-paging a").click(function() {
		$("#ccm-al-search-results").html("");	
		ccm_alShowLoader();
		$.ajax({
			type: "GET",
			url: $(this).attr('href'),
			success: function(resp) {
				ccm_alHideLoader();
				$("#ccm-al-search-results").html(resp);
				ccm_alSetupPaging();
			}
		});
		return false;
	});	
}

$(function() {
	/*
	 * pretty sure this is screwing up the throbber
	 $.ajaxSetup({async:false});
	*/
	
	$("#ccm-add-asset-link").click(function() { 
		$("#ccm-al-add-asset").show()
		$("#ccm-al").hide();
	});
	$("#ccm-al-refresh").click(function() { ccm_alRefresh(); });
	$("#ccm-exit-al").click(function() { ccm_exitAL(); });
	$(document).click(function(e) {
		e.stopPropagation();
		ccm_alSelectNone();
	});
	
	$("#ccm-al-search").ajaxForm({
			beforeSubmit: function() {
				ccm_alShowLoader();
				$("#ccm-al-search-results").html('');
				return true;
			},
			success: function(resp) {
				ccm_alHideLoader();
				$("#ccm-al-search-results").html(resp);	
				ccm_alSetupPaging(); 
			}
	});
	
	$('#ccm-al-search-button').get(0).click();	
	$("#fileSearch_bDateAdded").datepicker({
		showAnim: "fadeIn"
	});
	$("#ccm-button-browse").dialog();
});

</script>

<?
$fileTypes = FileSearch::getFileTypes();

if (is_array($assetLibraryPassThru)) {
	foreach($assetLibraryPassThru as $key => $value) {
		$_REQUEST[$key] = $value;	
	}
}

?>

<div id="ccm-al-add-asset">
<a id="ccm-button-browse" class="ccm-button" dialog-width="600" dialog-height="525" dialog-modal="false" dialog-title="<?=t('Add File')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/al_upload.php?cID=<?=$_REQUEST['cID']?>"><span><em class="ccm-button-add"><?=t('Add File')?></em></span></a>
</div>

<div class="ccm-spacer">&nbsp;</div>
<br/>
<div id="ccm-al">

<form method="get" id="ccm-al-search" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/al_search_results.php">

	<div style="margin:0px; padding:0px; width:100%; height:auto" >
	<table class="ccm-al-search-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header"><?=t('Filename')?></td>
		<td class="header"><?=t('Added on or after:')?></td>
		<td class="header"><?=t('Type')?></td>
		<td class="header"><?=t('Sort By')?></td>
		<td class="header"><?=t('View')?></td>
		<td class="header">&nbsp;</td>
	</tr>
	<tr>
		<td><input id="fileSearch_bFile" type="text" name="bFile" style="width: 100px" value="<?=$_REQUEST['bFile']?>"></td>
		<td style="white-space: nowrap"><input id="fileSearch_bDateAdded" type="text" style="width: 100px" name="bDateAdded" id="bDateAdded" value="<?=$_REQUEST['bDateAdded']?>">
		<td><select name="type">
				<option value="">* <?=t('All')?></option>
			<? foreach($fileTypes as $ft) { ?>
				<option value="<?=$ft?>"<? if ($_REQUEST['type'] == $ft) { ?> selected <? } ?>><?=$ft?></option>
			<? } ?>
		</select></td>
		<td><select id="fileSearchSorting" name="sort">
			<option value="origfilename"<? if ($_REQUEST['sort'] == 'filename') { ?> selected <? } ?>><?=t('Filename')?></option>
			<option value="bDateAdded desc"<? if ($_REQUEST['sort'] == 'bDateAdded desc') { ?> selected <? } ?>><?=t('Most Recent Files First')?></option>
			<option value="origfilename desc"<? if ($_REQUEST['sort'] == 'filename desc') { ?> selected <? } ?>><?=t('Filename descending')?></option>
			<option value="bDateAdded"<? if ($_REQUEST['sort'] == 'bDateAdded') { ?> selected <? } ?>><?=t('Earliest First')?></option>
		</select></td>		

		<td><select id="search_page_size" name="view">
			<option>5</option>
			<option selected="selected">25</option>
			<option>50</option>
			<option>100</option>
			<option>250</option>
		</select></td>

		<td style="text-align: center">
		<input type="submit" style="display: none" id="ccm-al-search-button" name="submit" />
		<a class="ccm-button-right accept" onclick="$('#ccm-al-search-button').get(0).click()" href="javascript:void(0)"><span><?=t('Search')?></span></a>
		</td>
	</tr>
	</table>
	</div>
	
</form>


<br/>

<div class="wrapper" id="ccm-al-search-results">

</div>
</div>