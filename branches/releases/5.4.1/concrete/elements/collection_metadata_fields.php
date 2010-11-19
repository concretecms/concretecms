<script>
$(function() {
	$("#ccm-meta-custom-fields").change(function() {
		if ($(this).val() != "" && typeof($(this).val()) != undefined) {
			var thisField = $(this).val();
			$("#ccm-field-ak" + $(this).val()).show();
			this.options[this.selectedIndex] = null;
			this.selectedIndex = 0;
			
			$("#ccm-meta-field-selected" + thisField).val(thisField);
		}
	});
	
	$("a.ccm-meta-close").click(function() {
		var thisField = $(this).attr('id').substring(19);
		var thisName = $(this).attr('ccm-meta-name');
		$("#ccm-meta-field-selected" + thisField).val(0);
		$("#ccm-field-ak" + thisField).hide();
		
		// add it back to the select menu
		$("#ccm-meta-custom-fields").each(function() {
			this.options[this.options.length] = new Option(thisName, thisField);
		});
				
	});

	$("a.ccm-meta-path-add").click(function(ev) { ccmPathHelper.add(ev.target) });
	$("a.ccm-meta-path-del").click(function(ev) { ccmPathHelper.del(ev.target) });
});

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
</script>


<div id="ccm-metadata-fields">
<?php 
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

	<h2>
		<?php echo t('Custom Attributes')?> 
		<select id="ccm-meta-custom-fields">
			<option value="">** <?php echo t('Add Attribute')?></option>
			<?php  $cAttributes = CollectionAttributeKey::getList(); 
			foreach($cAttributes as $ck) { 
				if (!in_array($ck->getAttributeKeyID(), $usedKeysCombined) || $c->getCollectionTypeID()==0) {?>
				<option value="<?php echo $ck->getAttributeKeyID()?>"><?php echo $ck->getAttributeKeyName()?></option>
			<?php  }
			
			}?>
		</select>
	</h2><br/>

	<?php  
		ob_start();
		
		$al = Loader::helper('concrete/asset_library');


		foreach($cAttributes as $ak) {
			$caValue = $c->getAttributeValueObject($ak); ?>
		
			<div class="ccm-field-meta" id="ccm-field-ak<?php echo $ak->getAttributeKeyID()?>" <?php  if (!in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?> style="display: none" <?php  } ?>>
			<input type="hidden" class="ccm-meta-field-selected" id="ccm-meta-field-selected<?php echo $ak->getAttributeKeyID()?>" name="selectedAKIDs[]" value="<?php  if (!in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?>0<?php  } else { ?><?php echo $ak->getAttributeKeyID()?><?php  } ?>" />
			
			<a href="javascript:void(0)" class="ccm-meta-close" ccm-meta-name="<?php echo $ak->getAttributeKeyName()?>" id="ccm-remove-field-ak<?php echo $ak->getAttributeKeyID()?>"
			  style="display:<?php echo (!in_array($ak->getAttributeKeyID(), $requiredKeys))?'block':'none'?>" ><?php echo t('Remove Attribute')?></a>
	
			
			<label><?php echo $ak->getAttributeKeyName()?></label>
			<?php echo $ak->render('form', $caValue); ?>
	
	
				<div class="ccm-spacer">&nbsp;</div>
				
			</div>
		<?php  } 
		$contents = ob_get_contents();
		ob_end_clean(); ?>	
		
		<script type="text/javascript">
		<?php  
		$v = View::getInstance();
		$headerItems = $v->getHeaderItems();
		foreach($headerItems as $item) {
			if ($item instanceof CSSOutputObject) {
				$type = 'CSS';
			} else {
				$type = 'JAVASCRIPT';
			} ?>
			 ccm_addHeaderItem("<?php echo $item->file?>", '<?php echo $type?>');
			<?php  
		} 
		?>
		</script>
		
		<?php  print $contents; ?>

</div>

