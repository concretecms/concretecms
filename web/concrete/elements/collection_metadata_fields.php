<? $form = Loader::helper('form'); ?>

<div class="row">
<div class="span4 columns">

	<div class="ccm-block-type-search-wrapper ">

		<form onsubmit="return ccmBlockTypeSearchFormCheckResults()">
		<div class="ccm-block-type-search">
		<?=$form->text('ccmBlockTypeSearch', array('tabindex' => 1, 'autocomplete' => 'off', 'style' => 'width: 155px'))?>
		</div>
		
		</form>
	</div>
	
	<?
	$category = AttributeKeyCategory::getByHandle('collection');
	$sets = $category->getAttributeSets();
	?>

	<ul id="ccm-page-attribute-list" class="icon-select-list">
	<? foreach($sets as $as) { ?>
		<li class="icon-select-list-header"><span><?=$as->getAttributeSetName()?></span></li>
		<? 
		$setattribs = $as->getAttributeKeys();
		foreach($setattribs as $ak) { ?>
			
			<li><a style="background-image: url('<?=$ak->getAttributeKeyIconSRC()?>')" href=""><?=$ak->getAttributeKeyName()?></a></li>	
		
		<? } 	
		
	} 
	
	if (count($sets) > 0 ) { ?>
		<li class="icon-select-list-header"><span><?=t('Other')?></span></li>
	<? }
	
	$unsetattribs = $category->getUnassignedAttributeKeys();
	foreach($unsetattribs as $ak) { ?>
		
		<li><a style="background-image: url('<?=$ak->getAttributeKeyIconSRC()?>')" href=""><?=$ak->getAttributeKeyName()?></a></li>	
	
	<? } 	

	?>
	</ul>
	
</div>
<div class="span7">
<div id="ccm-page-attributes-none" style="position: fixed">
<div style="padding-top: 140px; width: 400px; text-align: center"><h3><?=t('No attributes assigned.')?></h3></div>
</div>
</div>
</div>

