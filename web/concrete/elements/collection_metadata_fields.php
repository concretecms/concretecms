<? $form = Loader::helper('form'); ?>
<?
$attribs = array();

$requiredKeys = array();
$usedKeys = array();
if ($c->getCollectionTypeID() > 0) {
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
	
	if (count($sets) > 0 ) { ?>
		<li class="icon-select-list-header"><span><?=t('Other')?></span></li>
	<? }
	
	$unsetattribs = $category->getUnassignedAttributeKeys();
	foreach($unsetattribs as $ak) { ?>
		
		<li id="sak<?=$ak->getAttributeKeyID()?>" class="ccm-attribute-available <? if (in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?>ccm-attribute-added<? } ?>"><a style="background-image: url('<?=$ak->getAttributeKeyIconSRC()?>')" href="javascript:void(0)" onclick="ccmShowAttributeKey(<?=$ak->getAttributeKeyID()?>)"><?=$ak->getAttributeKeyName()?></a></li>	
	
	<? 
		$attribs[] = $ak;
	} 	
	
	?>
	</ul>
	
</div>
<div class="span7">
<div id="ccm-page-attributes-none" <? if (count($usedKeysCombined) > 0) { ?>style="display: none"<? } ?>>
<div style="padding-top: 140px; width: 400px; text-align: center"><h3><?=t('No attributes assigned.')?></h3></div>
</div>

<? 
	ob_start();

	foreach($attribs as $ak) {
		$caValue = $c->getAttributeValueObject($ak); ?>

	
		
		<div class="well form-stacked" id="ak<?=$ak->getAttributeKeyID()?>" <? if (!in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?> style="display: none" <? } ?>>
		<input type="hidden" class="ccm-meta-field-selected" id="ccm-meta-field-selected<?=$ak->getAttributeKeyID()?>" name="selectedAKIDs[]" value="<? if (!in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?>0<? } else { ?><?=$ak->getAttributeKeyID()?><? } ?>" />
		
			<a href="javascript:void(0)"><?=t('Remove Attribute')?></a>

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



<? foreach($attribs as $ak) { 
	$caValue = $c->getAttributeValueObject($ak);
?>
<div class="well form-stacked" id="ak<?=$ak->getAttributeKeyID()?>" style="display: none">
	<label><?=$ak->getAttributeKeyName()?></label>
	<?=$ak->render('form', $caValue); ?>
</div>
<? } ?>
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

$(function() {
	$(window).css('overflow', 'hidden');
	$(window).unbind('keydown.attribs');
	ccmPageAttributesMapKeys();
	$("#ccmSearchAttributeListField").get(0).focus();

});


</script>

