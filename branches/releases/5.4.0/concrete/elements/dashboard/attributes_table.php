<?php  
//Used on both page and file attributes
$c = Page::getCurrentPage();

$sets = array();
if (is_object($category) && $category->allowAttributeSets()) {
	$sets = $category->getAttributeSets();
}

if (count($attribs) > 0) { 

	$ih = Loader::helper('concrete/interface');
	$valt = Loader::helper('validation/token');
	
	if (count($sets) > 0) {  ?>
	
		<h3 style="position: absolute; top: 6px; right: 8px"><?php echo t('View Attributes: ')?><select style="font-size: 10px" onchange="window.location.href='<?php echo Loader::helper('navigation')->getLinkToCollection($c)?>?asGroupAttributes=' + this.value" name="asGroupAttributes">
			<option value="1" <?php  if ($_REQUEST['asGroupAttributes'] !== '0') { ?> selected <?php  } ?>><?php echo t('Grouped by set')?></option>
			<option value="0" <?php  if ($_REQUEST['asGroupAttributes'] === '0') { ?> selected <?php  } ?>><?php echo t('In one list')?></option>
		</select></h3>
		<div class="ccm-spacer">&nbsp;</div>

	<?php  }
	
	if (count($sets) > 0 && ($_REQUEST['asGroupAttributes'] !== '0')) { ?>
	
	
		<?php 
	
		foreach($sets as $as) { ?>
	
		
		<h2><?php echo $as->getAttributeSetName()?></h2>
	
		<?php 
		
		$setattribs = $as->getAttributeKeys();
		if (count($setattribs) == 0) { ?>
		
			<?php echo t('No attributes defined.')?><br/><br/>
		
		<?php  } else { ?>
			
			<div class="ccm-attribute-sortable-set-list" attribute-set-id="<?php echo $as->getAttributeSetID()?>" id="asID_<?php echo $as->getAttributeSetID()?>">			
			
			<?php 
			
			foreach($setattribs as $ak) { ?>
			
			<div class="ccm-attribute" id="akID_<?php echo $as->getAttributeSetID()?>_<?php echo $ak->getAttributeKeyID()?>">
				<img class="ccm-attribute-icon" src="<?php echo $ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><a href="<?php echo $this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?php echo $ak->getAttributeKeyName()?></a>
			</div>
	

			<?php  } ?>
			
			</div>
			
			<?php  } ?>
			
			<br/>
			
		<?php  } 
		
		$unsetattribs = $category->getUnassignedAttributeKeys();
		if (count($unsetattribs) > 0) { ?>
		
			<h2><?php echo t('Other')?></h2>
		
			<?php 
			foreach($unsetattribs as $ak) { ?>
	
			<div class="ccm-attribute" id="akID_<?php echo $as->getAttributeSetID()?>_<?php echo $ak->getAttributeKeyID()?>">
				<img class="ccm-attribute-icon" src="<?php echo $ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><a href="<?php echo $this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?php echo $ak->getAttributeKeyName()?></a>
			</div>
	

			<?php  } ?>
		
		<?php 
		
		}
	
	} else { ?>
		
		<div class="ccm-attributes-list">
		
		<?php 
		foreach($attribs as $ak) { ?>
		<div class="ccm-attribute" id="akID_<?php echo $ak->getAttributeKeyID()?>">
			<img class="ccm-attribute-icon" src="<?php echo $ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><a href="<?php echo $this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?php echo $ak->getAttributeKeyName()?></a>
		</div>
		
		<?php  } ?>
	
		</div>
	
	<?php  } ?>
	
<?php  } else { ?>
	
	<br/>
	
	<strong>
		<?php 
	 echo t('No attributes defined.');
		?>
	</strong>
	
	<br/><br/>
	
<?php  } ?>

<script type="text/javascript">
	$("div.ccm-attribute-sortable-set-list").sortable({
		handle: 'img.ccm-attribute-icon',
		cursor: 'move',
		opacity: 0.5,
		stop: function() {
			var ualist = $(this).sortable('serialize');
			ualist += '&cID=<?php echo $c->getCollectionID()?>';
			ualist += '&asID=' + $(this).attr('attribute-set-id');
			$.post('<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/attribute_sets_update', ualist, function(r) {

			});
		}
	});
</script>

<style type="text/css">
div.ccm-attribute-sortable-set-list img.ccm-attribute-icon:hover {cursor: move}
</style>
