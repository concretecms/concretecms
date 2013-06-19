<? 
defined('C5_EXECUTE') or die("Access Denied.");
$btl = new BlockTypeList();
$blockTypes = $btl->getBlockTypeList();
$dsh = Loader::helper('concrete/dashboard');
$dashboardBlockTypes = array();
if ($dsh->inDashboard()) {
	$dashboardBlockTypes = BlockTypeList::getDashboardBlockTypes();
}
$blockTypes = array_merge($blockTypes, $dashboardBlockTypes);
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');
$form = Loader::helper('form');

?>

<script type="text/javascript">

ccm_showBlockTypeDescription = function(btID) {
	$("#ccm-bt-help" + btID).show();
}

ccm_showBlockTypeDescriptions = function() {
	$(".ccm-block-type-description").show();
}

var ccm_areaActiveTab = "ccm-add";

$("#ccm-area-tabs a").click(function() {
	$("li.active").removeClass('active');
	$("#" + ccm_areaActiveTab + "-tab").hide();
	ccm_areaActiveTab = $(this).attr('id');
	$(this).parent().addClass("active");
	$("#" + ccm_areaActiveTab + "-tab").show();
	if (ccm_areaActiveTab == 'ccm-add-marketplace') {
		ccm_updateMarketplaceTab();	
	}
});

$('input[name=ccmBlockTypeSearch]').focus(function() {
	if ($(this).val() == '<?=t("Search")?>') {
		$(this).val('');
	}
	$(this).css('color', '#000');

	if (!ccmLiveSearchActive) {
		$('#ccmBlockTypeSearch').liveUpdate('ccm-block-type-list');
		ccmLiveSearchActive = true;
//		$("#ccm-block-type-clear-search").show();
	}
});

ccmBlockTypeSearchFormCheckResults = function() {
	return false;
}

ccmBlockTypeSearchClear = function() {
	$("input[name=ccmBlockTypeSearch]").val('');
	$("#ccm-block-type-list li.ccm-block-type").addClass("ccm-block-type-available");
	$("#ccm-block-type-list li.ccm-block-type").removeClass("ccm-block-type-selected");
}

var ccmLiveSearchActive = false;
ccmBlockTypeSearchResultsSelect = function(which, e) {

	e.preventDefault();
	e.stopPropagation();
//	$("input[name=ccmBlockTypeSearch]").blur();

	// find the currently selected item
	var obj = $("li.ccm-block-type-selected");
	var foundblock = false;
	if (obj.length == 0) {
		$($("#ccm-block-type-list li.ccm-block-type-available")[0]).addClass('ccm-block-type-selected');
	} else {
		if (which == 'next') {
			var nextObj = obj.nextAll('li.ccm-block-type-available');
			if (nextObj.length > 0) {
				obj.removeClass('ccm-block-type-selected');
				$(nextObj[0]).addClass('ccm-block-type-selected');
			}
		} else if (which == 'previous') {
			var prevObj = obj.prevAll('li.ccm-block-type-available');
			if (prevObj.length > 0) {
				obj.removeClass('ccm-block-type-selected');
				$(prevObj[0]).addClass('ccm-block-type-selected');
			}
		}
		
	}	

	var currObj = $("li.ccm-block-type-selected");
	// handle scrolling
	// this is buggy. needs fixing

	var currPos = currObj.position();
	var currDialog = currObj.parents('div.ui-dialog-content');
	var docViewTop = currDialog.scrollTop();
	var docViewBottom = docViewTop + currDialog.innerHeight();

	var elemTop = currObj.position().top;
	var elemBottom = elemTop + docViewTop + currObj.innerHeight();

	if ((docViewBottom - elemBottom) < 0) {
		currDialog.get(0).scrollTop += currDialog.get(0).scrollTop + currObj.height();
	} else if (elemTop < 0) {
		currDialog.get(0).scrollTop -= currDialog.get(0).scrollTop + currObj.height();
	}


	return true;
	
}

ccmBlockTypeDoMapKeys = function(e) {
	/*
	if (e.keyCode == 9) {
		e.stopPropagation();
		e.preventDefault();
		$("input[name=ccmBlockTypeSearch]").focus();
		return true;
	}
	if (e.keyCode == 8) {
		$("input[name=ccmBlockTypeSearch]").val('');
		e.stopPropagation();
		e.preventDefault();
		return true;
	}
	*/

	if (e.keyCode == 40) {
		ccmBlockTypeSearchResultsSelect('next', e);
	} else if (e.keyCode == 38) {
		ccmBlockTypeSearchResultsSelect('previous', e);
	} else if (e.keyCode == 13) {
		var obj = $("li.ccm-block-type-selected");
		if (obj.length > 0) {
			obj.find('a').click();
		}
	}
}
ccmBlockTypeMapKeys = function() {
	$(window).bind('keydown.blocktypes', ccmBlockTypeDoMapKeys);
}
ccmBlockTypeResetKeys = function() {
	$(window).unbind('keydown.blocktypes');
}

$(function() {
	$(window).css('overflow', 'hidden');
	$(window).unbind('keydown.blocktypes');
	ccmBlockTypeMapKeys();
	$('.ccm-block-type-help').tooltip();
	$("#ccmBlockTypeSearch").get(0).focus();

});

</script>


<div id="ccm-add-tab" class="ccm-ui">
	<div class="ccm-pane-options">
		<div class="ccm-block-type-search-wrapper ccm-pane-options-permanent-search">

		<form onsubmit="return ccmBlockTypeSearchFormCheckResults()">
		
		
		<a class="ccm-block-type-help" href="javascript:ccm_showBlockTypeDescriptions()" title="<?=t('Learn more about these block types.')?>" id="ccm-bt-help-trigger-all"><i class="icon-question-sign"></i></a>
		
		<i class="icon-search"></i>

		<?=$form->text('ccmBlockTypeSearch', array('tabindex' => 1, 'autocomplete' => 'off', 'style' => 'margin-left: 8px; width: 168px'))?>
		<a href="javascript:void(0)" id="ccm-block-type-clear-search" onclick="ccmBlockTypeSearchClear()"><img width="16" height="16" src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" border="0" style="vertical-align: middle" /></a>
		
		</form>
		
		</div>
	</div>
	
	
	<ul id="ccm-block-type-list">
	<? if (count($blockTypes) > 0) { 
		foreach($blockTypes as $bt) { 
			if (!$ap->canAddBlock($bt)) {
				continue;
			}
			$btIcon = $ci->getBlockTypeIconURL($bt);
			?>	
			<li class="ccm-block-type ccm-block-type-available">
				<? if (!$bt->hasAddTemplate()) { ?>
					<a style="background-image: url(<?=$btIcon?>)" href="javascript:void(0)" onclick="ccmBlockTypeResetKeys(); jQuery.fn.dialog.showLoader(); $.get('<?=$bt->getBlockAddAction($a)?>&processBlock=1&add=1', function(r) { ccm_parseBlockResponse(r, false, 'add'); })" class="ccm-block-type-inner"><?=t($bt->getBlockTypeName())?></a>
				<? } else { ?>
					<a onclick="ccmBlockTypeResetKeys()" dialog-on-destroy="ccmBlockTypeMapKeys()" class="dialog-launch ccm-block-type-inner" dialog-on-close="ccm_blockWindowAfterClose()" dialog-append-buttons="true" dialog-modal="false" dialog-width="<?=$bt->getBlockTypeInterfaceWidth()?>" dialog-height="<?=$bt->getBlockTypeInterfaceHeight()+20?>" style="background-image: url(<?=$btIcon?>)" dialog-title="<?=tc('%s is a block type name', 'Add %s', t($bt->getBlockTypeName()))?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/add_block_popup.php?cID=<?=$c->getCollectionID()?>&btID=<?=$bt->getBlockTypeID()?>&arHandle=<?=urlencode($a->getAreaHandle())?>"><?=t($bt->getBlockTypeName())?></a>
				<? } ?>
				<div class="ccm-block-type-description"  id="ccm-bt-help<?=$bt->getBlockTypeID()?>"><?=t($bt->getBlockTypeDescription())?></div>
			</li>
			<?
			
			/* ?>	
			<div class="ccm-block-type-grid-entry">
				<a class="dialog-launch ccm-block-type-inner" dialog-modal="false" dialog-width="<?=$bt->getBlockTypeInterfaceWidth()?>" dialog-height="<?=$bt->getBlockTypeInterfaceHeight()?>" style="background-image: url(<?=$btIcon?>)" dialog-title="<?=tc('%s is a block type name', 'Add %s', t($bt->getBlockTypeName()))?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/add_block_popup.php?cID=<?=$c->getCollectionID()?>&btID=<?=$bt->getBlockTypeID()?>&arHandle=<?=$a->getAreaHandle()?>"><?=t($bt->getBlockTypeName())?></a>
			</div> <? */ ?>
			
		<? }
	} else { ?>
		<p><?=t('No block types can be added to this area.')?></p>
	<? } ?>
	</ul>
</div>

<? if(ENABLE_MARKETPLACE_SUPPORT){ 
	$tp = new TaskPermission();
	if ($tp->canInstallPackages()) { 
	?>
	<div class="ccm-ui">

	<div class="well" style="padding:10px 20px;">
        <h3><?=t('More Blocks')?></h3>
        <p><?=t('Browse our marketplace of add-ons to extend your site!')?></p>
        <p><a class="btn success" href="javascript:void(0)" onclick="ccm_openAddonLauncher()"><?=t("More Add-ons")?></a></p>
    </div>

	</div>
<? } 

}?>	
