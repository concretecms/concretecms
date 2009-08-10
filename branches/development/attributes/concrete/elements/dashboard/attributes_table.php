<? 
//Used on both page and file attributes

if (count($attribs) > 0) { 

	$ih = Loader::helper('concrete/interface');
	$valt = Loader::helper('validation/token');
	
	?>
	
	<div class="ccm-attributes-list">
	
	<?
	foreach($attribs as $ak) { ?>
	<div class="ccm-attribute" id="akID_<?=$ak->getAttributeKeyID()?>">
		<img class="ccm-attribute-icon" src="<?=$ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><a href="<?=$this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?=$ak->getAttributeKeyName()?></a>
	</div>
	
	<? } ?>

	</div>

<? } else { ?>
	
	<br/>
	
	<strong>
		<?
	 echo t('No attributes defined.');
		?>
	</strong>
	
	<br/><br/>
	
<? } ?>