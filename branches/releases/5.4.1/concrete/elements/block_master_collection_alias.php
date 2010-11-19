<?php 
defined('C5_EXECUTE') or die("Access Denied.");
global $c;

// grab all the collections belong to the collection type that we're looking at
Loader::model('collection_types');
$ctID = $c->getCollectionTypeID();
$ct = CollectionType::getByID($ctID);

$cList = $ct->getPages();
?>

<form method="post" id="ccmBlockMasterCollectionForm" action="<?php echo $b->getBlockMasterCollectionAliasAction()?>">

	<?php  if (count($cList) == 0) { ?>
	
	<?php echo t("There are no pages of this type added to your website. If there were, you'd be able to choose which of those pages this block appears on.")?>
	
	<?php  } else { ?>
	
	<p><?php echo t("Choose which pages below this particular block should appear on. Any previously selected blocks may also be removed using the checkbox. Click the checkbox in the header to select/deselect all pages.")?></p>
	<br/>
		
		<table border="0" cellspacing="0" width="100%" class="ccm-grid" cellpadding="0">
		<tr>
			<th>ID</th>
			<th><?php echo t('Name')?></th>
			<th style="white-space: nowrap"><?php echo t('Date Created')?></th>
			<th style="white-space: nowrap"><?php echo t('Date Modified')?></th>			
			<th style="white-space: nowrap"><input type="checkbox" id="mc-cb-all" /></th>			
		</tr>
	
	<?php 
		
		foreach($cList as $p) { ?>
			<tr class="active">
			<td><?php echo $p->getCollectionID()?></td>
			<td><a href="<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $p->getCollectionID()?>" target="_blank"><?php echo $p->getCollectionName()?></a></td>
			<td style="text-align: center"><?php echo $p->getCollectionDateAdded('m/d/Y','user')?></td>
			<td style="text-align: center"><?php  if ($b->isAlias($p)) { ?> <input type="hidden" name="checkedCIDs[]" value="<?php echo $p->getCollectionID()?>" /><?php  } ?><?php echo $p->getCollectionDateLastModified('m/d/Y','user')?></td>
			<td style="text-align: center"><input class="mc-cb" type="checkbox" name="cIDs[]" value="<?php echo $p->getCollectionID()?>" <?php  if ($b->isAlias($p)) { ?> checked <?php  } ?> /></td>
			</tr>
		
		<?php  } ?>
	
		</table>
		
	<?php  } ?>
	
	<div class="ccm-buttons">
	<a href="#" class="ccm-dialog-close ccm-button-left cancel"><span><em class="ccm-button-close"><?php echo t('Cancel')?></em></span></a>
	<a href="javascript:void(0)" onclick="$('#ccmBlockMasterCollectionForm').submit()" class="ccm-button-right accept"><span><?php echo t('Update')?></span></a>
	</div>

<script type="text/javascript">
$(function() {
	$('#mc-cb-all').click(function() {
		if (this.checked) {
			$('input.mc-cb').each(function() {
				$(this).get(0).checked = true;
			});
		} else {
			$('input.mc-cb').each(function() {
				$(this).get(0).checked = false;
			});
		}
	});
	$('#ccmBlockMasterCollectionForm').each(function() {
		ccm_setupBlockForm($(this), '<?php echo $b->getBlockID()?>', 'edit');
	});

});

</script>
</form>