<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$btl = $a->getAddBlockTypes($c, $ap );
$blockTypes = $btl->getBlockTypeList();
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');
$form = Loader::helper('form');

?>

<script type="text/javascript">
<?php  if (ENABLE_MARKETPLACE_SUPPORT) { ?>

function ccm_updateMarketplaceTab() {
	if (!ccm_blocksLoaded) {
		$("#ccm-add-marketplace-tab div.ccm-block-type-list").html('');
		jQuery.fn.dialog.showLoader();
		$.ajax({
			url: CCM_TOOLS_PATH+'/marketplace/refresh_block',
			type: 'POST',
			data: {'arHandle': '<?php echo $a->getAreaHandle()?>'},
			success: function(html) {
				jQuery.fn.dialog.hideLoader();
				$("#ccm-add-marketplace-tab div.ccm-block-type-list").html(html);
			}
		});
		ccm_blocksLoaded = true;
	}
}

var ccm_blocksLoaded = false;

<?php  } ?>

ccm_showBlockTypeDescription = function(btID) {
	$("#ccm-bt-help" + btID).show();
}

ccm_showBlockTypeDescriptions = function() {
	$(".ccm-block-type-description").show();
}

var ccm_areaActiveTab = "ccm-add";

$("#ccm-area-tabs a").click(function() {
	$("li.ccm-nav-active").removeClass('ccm-nav-active');
	$("#" + ccm_areaActiveTab + "-tab").hide();
	ccm_areaActiveTab = $(this).attr('id');
	$(this).parent().addClass("ccm-nav-active");
	$("#" + ccm_areaActiveTab + "-tab").show();
	if (ccm_areaActiveTab == 'ccm-add-marketplace') {
		ccm_updateMarketplaceTab();	
	}
});

$('input[name=ccmBlockTypeSearch]').focus(function() {
	if ($(this).val() == '<?php echo t("Search")?>') {
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
	$("input[name=ccmBlockTypeSearch]").blur();
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
	var currDialog = currObj.parents('div.ccm-dialog-content');
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
	$("input[name=ccmBlockTypeSearch]").focus();
	ccmBlockTypeMapKeys();
});

</script>


<?php  if (ENABLE_MARKETPLACE_SUPPORT && $_REQUEST['addOnly'] != 1) { ?>
<ul class="ccm-dialog-tabs" id="ccm-area-tabs">
	<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-add"><?php echo t('Add New')?></a></li>
	<li><a href="javascript:void(0)" id="ccm-add-marketplace"><?php echo t('Add From Marketplace')?></a></li>
</ul>
<?php  } ?>

<div id="ccm-add-tab">
	<div class="ccm-block-type-search-wrapper">

		<a class="ccm-block-type-help" href="javascript:ccm_showBlockTypeDescriptions()" title="<?php echo t('Learn more about this block type.')?>" id="ccm-bt-help-trigger-all"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/icon_header_help.png" width="17" height="20" /></a>

		<form onsubmit="return ccmBlockTypeSearchFormCheckResults()">
		<div class="ccm-block-type-search">
		<?php echo $form->text('ccmBlockTypeSearch', array('tabindex' => 1, 'autocomplete' => 'off', 'style' => 'width: 168px'))?>
		<a href="javascript:void(0)" id="ccm-block-type-clear-search" onclick="ccmBlockTypeSearchClear()"><img width="16" height="16" src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" border="0" style="vertical-align: middle" /></a>
		</div>
		
		</form>
		
	</div>
	
	<ul id="ccm-block-type-list">
	<?php  if (count($blockTypes) > 0) { 
		foreach($blockTypes as $bt) { 
			$btIcon = $ci->getBlockTypeIconURL($bt);
			?>	
			<li class="ccm-block-type ccm-block-type-available">
				<a onclick="ccmBlockTypeResetKeys()" dialog-on-destroy="ccmBlockTypeMapKeys()" class="dialog-launch ccm-block-type-inner" dialog-on-close="ccm_blockWindowAfterClose()" dialog-modal="false" dialog-width="<?php echo $bt->getBlockTypeInterfaceWidth()?>" dialog-height="<?php echo $bt->getBlockTypeInterfaceHeight()?>" style="background-image: url(<?php echo $btIcon?>)" dialog-title="<?php echo t('Add')?> <?php echo $bt->getBlockTypeName()?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/add_block_popup.php?cID=<?php echo $c->getCollectionID()?>&btID=<?php echo $bt->getBlockTypeID()?>&arHandle=<?php echo urlencode($a->getAreaHandle())?>"><?php echo $bt->getBlockTypeName()?></a>
				<div class="ccm-block-type-description"  id="ccm-bt-help<?php echo $bt->getBlockTypeID()?>"><?php echo $bt->getBlockTypeDescription()?></div>
			</li>
			<?php 
			
			/* ?>	
			<div class="ccm-block-type-grid-entry">
				<a class="dialog-launch ccm-block-type-inner" dialog-modal="false" dialog-width="<?php echo $bt->getBlockTypeInterfaceWidth()?>" dialog-height="<?php echo $bt->getBlockTypeInterfaceHeight()?>" style="background-image: url(<?php echo $btIcon?>)" dialog-title="<?php echo t('Add')?> <?php echo $bt->getBlockTypeName()?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/add_block_popup.php?cID=<?php echo $c->getCollectionID()?>&btID=<?php echo $bt->getBlockTypeID()?>&arHandle=<?php echo $a->getAreaHandle()?>"><?php echo $bt->getBlockTypeName()?></a>
			</div> <?php  */ ?>
			
		<?php  }
	} else { ?>
		<p><?php echo t('No block types can be added to this area.')?></p>
	<?php  } ?>
	</ul>
</div>

<?php  if(ENABLE_MARKETPLACE_SUPPORT){ ?>
<div id="ccm-add-marketplace-tab" style="display: none">
	<div class="ccm-block-type-list">

	</div>
</div>
<?php  } ?>