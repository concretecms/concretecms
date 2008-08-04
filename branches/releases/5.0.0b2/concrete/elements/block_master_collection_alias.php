<? global $c;

// grab all the collections belong to the collection type that we're looking at
Loader::model('collection_types');
$ctID = $c->getCollectionTypeID();
$ct = CollectionType::getByID($ctID);

$cList = $ct->getPages();
?>

<form method="post" id="ccmBlockMasterCollectionForm" action="<?=$b->getBlockMasterCollectionAliasAction()?>">

	<? if (count($cList) == 0) { ?>
	
	There are no pages of this type added to your website. If there were, you'd be able to choose which of those pages this block appears on.
	
	<? } else { ?>
	
	<p>Choose which pages below this particular block should appear on. Any previously selected blocks may also be removed using the checkbox. Click the checkbox in the header to select/deselect all pages.</p>
	<br/>
		
		<table border="0" cellspacing="0" width="100%" class="ccm-grid" cellpadding="0">
		<tr>
			<th>ID</th>
			<th style="width: 100%">Name</th>
			<th style="white-space: nowrap">Page Created</th>
			<th style="white-space: nowrap">Page Modified</th>			
			<th style="white-space: nowrap">Block Added <input type="checkbox" id="mc-cb-all" /></th>			
		</tr>
	
	<?
		
		foreach($cList as $p) { ?>
			<tr class="active">
			<td><?=$p->getCollectionID()?></td>
			<td><a href="<?=DIR_REL?>/index.php?cID=<?=$p->getCollectionID()?>" target="_blank"><?=$p->getCollectionName()?></a></td>
			<td style="text-align: center"><?=$p->getCollectionDateAdded('m/d/Y')?></td>
			<td style="text-align: center"><? if ($b->isAlias($p)) { ?> <input type="hidden" name="checkedCIDs[]" value="<?=$p->getCollectionID()?>" /><? } ?><?=$p->getCollectionDateLastModified('m/d/Y')?></td>
			<td style="text-align: center"><input class="mc-cb" type="checkbox" name="cIDs[]" value="<?=$p->getCollectionID()?>" <? if ($b->isAlias($p)) { ?> checked <? } ?> /></td>
			</tr>
		
		<? } ?>
	
		</table>
		
	<? } ?>
	
	<div class="ccm-buttons">
	<a href="#" class="ccm-dialog-close ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>
	<a href="javascript:$('#ccmBlockMasterCollectionForm').get(0).submit()" class="ccm-button-right accept"><span>Update</span></a>
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
});

</script>
</form>