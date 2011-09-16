<? $form = Loader::helper('form'); ?>
<?
$attribs = array();

$requiredKeys = array();
$usedKeys = array();
if ($c->getCollectionTypeID() > 0 && !$c->isMasterCollection()) {
	$cto = CollectionType::getByID($c->getCollectionTypeID());
	$aks = $cto->getAvailableAttributeKeys();
	foreach($aks as $ak) {
		$requiredKeys[] = $ak->getAttributeKeyID();
	}
}
$setAttribs = $c->getSetCollectionAttributes();
foreach($setAttribs as $ak) {
	$usedKeys[] = $ak->getAttributeKeyID();
}
$usedKeysCombined = array_merge($requiredKeys, $usedKeys);

?>

<div class="row">
<div class="span4 columns">
	<h6><?=t("All Attributes")?></h6>
	<div class="ccm-block-type-search-wrapper ">

		<form onsubmit="return ccmPageAttributeSearchFormCheckResults()">
		<div class="ccm-block-type-search">
		<?=$form->text('ccmSearchAttributeListField', array('tabindex' => 1, 'autocomplete' => 'off', 'style' => 'width: 155px'))?>
		</div>
		
		</form>
	</div>
	
	<?
	$category = AttributeKeyCategory::getByHandle('collection');
	$sets = $category->getAttributeSets();
	?>

	<ul id="ccm-page-attribute-list" class="icon-select-list">
	<? foreach($sets as $as) { ?>
		<li class="icon-select-list-header ccm-attribute-available"><span><?=$as->getAttributeSetName()?></span></li>
		<? 
		$setattribs = $as->getAttributeKeys();
		foreach($setattribs as $ak) { ?>
			
			<li id="sak<?=$ak->getAttributeKeyID()?>" class="ccm-attribute-available <? if (in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?>ccm-attribute-added<? } ?>"><a style="background-image: url('<?=$ak->getAttributeKeyIconSRC()?>')" href="javascript:void(0)" onclick="ccmShowAttributeKey(<?=$ak->getAttributeKeyID()?>)"><?=$ak->getAttributeKeyName()?></a></li>	
			
		<? 
			$attribs[] = $ak;
		} 	
		
	} 

	$unsetattribs = $category->getUnassignedAttributeKeys();
	
	if (count($sets) > 0 && count($unsetattribs) > 0) { ?>
		<li class="icon-select-list-header"><span><?=t('Other')?></span></li>
	<? }
	
	foreach($unsetattribs as $ak) { ?>
		
		<li id="sak<?=$ak->getAttributeKeyID()?>" class="ccm-attribute-available <? if (in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?>ccm-attribute-added<? } ?>"><a style="background-image: url('<?=$ak->getAttributeKeyIconSRC()?>')" href="javascript:void(0)" onclick="ccmShowAttributeKey(<?=$ak->getAttributeKeyID()?>)"><?=$ak->getAttributeKeyName()?></a></li>	
	
	<? 
		$attribs[] = $ak;
	} 	
	
	?>
	</ul>
	
</div>
<div class="span7">
<h6><?=t("Selected Attributes")?></h6>
<div id="ccm-page-attributes-none" <? if (count($usedKeysCombined) > 0) { ?>style="display: none"<? } ?>>
<div style="padding-top: 140px; width: 400px; text-align: center"><h3>
	<? if ($c->isMasterCollection()) { ?>
		<?=t('No attributes assigned. Any attributes you set here will automatically be set on pages when they are created.')?>
	<? } else { ?>
		<?=t('No attributes assigned.')?>
	<? } ?></h3></div>
</div>

<? 
	ob_start();

	foreach($attribs as $ak) {
		$caValue = $c->getAttributeValueObject($ak); ?>

	
		
		<div class="well form-stacked" id="ak<?=$ak->getAttributeKeyID()?>" <? if (!in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?> style="display: none" <? } ?>>
		<input type="hidden" class="ccm-meta-field-selected" id="ccm-meta-field-selected<?=$ak->getAttributeKeyID()?>" name="selectedAKIDs[]" value="<? if (!in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?>0<? } else { ?><?=$ak->getAttributeKeyID()?><? } ?>" />
		
			<a href="javascript:void(0)" class="ccm-meta-close" ccm-meta-name="<?=$ak->getAttributeKeyName()?>" id="ccm-remove-field-ak<?=$ak->getAttributeKeyID()?>" style="display:<?=(!in_array($ak->getAttributeKeyID(), $requiredKeys))?'block':'none'?>"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" alt="<?=t('remove')?>" /></a>

			<label><?=$ak->getAttributeKeyName()?></label>
			<?=$ak->render('form', $caValue); ?>
		</div>
	<? } 
	$contents = ob_get_contents();
	ob_end_clean(); ?>	
	
	<script type="text/javascript">
	<? 
	$v = View::getInstance();
	$headerItems = $v->getHeaderItems();
	foreach($headerItems as $item) {
		if ($item instanceof CSSOutputObject) {
			$type = 'CSS';
		} else {
			$type = 'JAVASCRIPT';
		} ?>
		 ccm_addHeaderItem("<?=$item->file?>", '<?=$type?>');
		<? 
	} 
	?>
	</script>
	
	<? print $contents; ?>



</div>
</div>

<script type="text/javascript">

$('input[name=ccmSearchAttributeListField]').focus(function() {
	$(this).css('color', '#666');
	if (!ccmLiveSearchActive) {
		$('#ccmSearchAttributeListField').liveUpdate('ccm-page-attribute-list', 'attributes');
		ccmLiveSearchActive = true;
	}
});

var ccmLiveSearchActive = false;
ccmBlockTypeSearchResultsSelect = function(which, e) {

	e.preventDefault();
	e.stopPropagation();
//	$("input[name=ccmBlockTypeSearch]").blur();

	// find the currently selected item
	var obj = $("li.ccm-item-selected");
	var foundblock = false;
	if (obj.length == 0) {
		$($("#ccm-page-attribute-list li.ccm-attribute-available:not(.icon-select-list-header)")[0]).addClass('ccm-item-selected');
	} else {
		if (which == 'next') {
			var nextObj = obj.nextAll('li.ccm-attribute-available:not(.icon-select-list-header)');
			if (nextObj.length > 0) {
				obj.removeClass('ccm-item-selected');
				$(nextObj[0]).addClass('ccm-item-selected');
			}
		} else if (which == 'previous') {
			var prevObj = obj.prevAll('li.ccm-attribute-available:not(.icon-select-list-header)');
			if (prevObj.length > 0) {
				obj.removeClass('ccm-item-selected');
				$(prevObj[0]).addClass('ccm-item-selected');
			}
		}
		
	}	

	var currObj = $("li.ccm-item-selected");

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

ccmPageAttributesDoMapKeys = function(e) {

	if (e.keyCode == 40) {
		ccmBlockTypeSearchResultsSelect('next', e);
	} else if (e.keyCode == 38) {
		ccmBlockTypeSearchResultsSelect('previous', e);
	} else if (e.keyCode == 13) {
		var obj = $("li.ccm-item-selected");
		if (obj.length > 0) {
			obj.find('a').click();
		}
	}
}
ccmPageAttributesMapKeys = function() {
	$(window).bind('keydown.attribs', ccmPageAttributesDoMapKeys);
}

ccmShowAttributeKey = function(akID) {

	$("#ccm-page-attributes-none").hide();
	$("#sak" + akID).addClass('ccm-attribute-added');
	$("#ak" + akID).find('.ccm-meta-field-selected').val(akID);
	$("#ak" + akID).fadeIn(300, 'easeOutExpo');
}

var ccmPathHelper={
	add:function(field){
		var parent = $(field).parent();
		var clone = parent.clone();
		clone.children().each(function() {
			if (this.id != undefined  && (i = this.id.search("-add-")) != -1) {
				this.id = this.id.substr(0, i) + "-add-" + (parseInt(this.id.substr(i+5)) + 1);
			}
			if (this.name != undefined && (i = this.name.search("-add-")) != -1) {
				this.name = this.name.substr(0, i) + "-add-" + (parseInt(this.name.substr(i+5)) + 1);
			}
			if (this.type == "text") {
				this.value = "";
			}
		});
    	$(field).replaceWith('<a href="javascript:void(0)" class="ccm-meta-path-del">Remove Path</a>');
		clone.appendTo(parent.parent());

		$("a.ccm-meta-path-add,a.ccm-meta-path.del").unbind('click');
		$("a.ccm-meta-path-add").click(function(ev) { ccmPathHelper.add(ev.target) });
		$("a.ccm-meta-path-del").click(function(ev) { ccmPathHelper.del(ev.target) });
	},
	del:function(field){
		$(field).parent().remove();
	}
}
$(function() {
	$(window).css('overflow', 'hidden');
	$(window).unbind('keydown.attribs');
	ccmPageAttributesMapKeys();
	$("#ccmSearchAttributeListField").get(0).focus();

	$("a.ccm-meta-close").click(function() {
		var thisField = $(this).attr('id').substring(19);
		var thisName = $(this).attr('ccm-meta-name');
		$("#ccm-meta-field-selected" + thisField).val(0);
		// add it back to the select menu
		$("#sak" + thisField).removeClass('ccm-attribute-added');
		$("#ak" + thisField).fadeOut(80, 'easeOutExpo', function() {
			if ($('li.ccm-attribute-added').length == 0) {
				$("#ccm-page-attributes-none").show();
			}
		});
		
	});

	$("a.ccm-meta-path-add").click(function(ev) { ccmPathHelper.add(ev.target) });
	$("a.ccm-meta-path-del").click(function(ev) { ccmPathHelper.del(ev.target) });

});


</script>

<style type="text/css">
#ccm-properties-custom-tab input.ccm-input-text {
	width: 350px;
}
#ccm-properties-custom-tab textarea.ccm-input-textarea {
	width: 350px;
}

</style>

