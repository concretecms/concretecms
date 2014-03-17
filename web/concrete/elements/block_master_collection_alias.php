<?
defined('C5_EXECUTE') or die("Access Denied.");
$c  = Page::getCurrentPage();

// grab all the collections belong to the collection type that we're looking at
Loader::model('collection_types');
$ptID = $c->getPageTypeID();
$ct = PageType::getByID($ptID);

$cList = $ct->getPages();
?>
<div class="ccm-ui">
<form method="post" id="ccmBlockMasterCollectionForm" data-dialog-form="master-collection-alias" action="<?=$b->getBlockMasterCollectionAliasAction()?>">

	<? if (count($cList) == 0) { ?>
	
	<?=t("There are no pages of this type added to your website. If there were, you'd be able to choose which of those pages this block appears on.")?>
	
	<? } else { ?>
	
	<p><?=t("Choose which pages below this particular block should appear on. Any previously selected blocks may also be removed using the checkbox. Click the checkbox in the header to select/deselect all pages.")?></p>
	<br/>
		
		<table class="table-striped table table-bordered" >
		<tr>
			<th>ID</th>
			<th><?=t('Name')?></th>
			<th ><?=t('Date Created')?></th>
			<th ><?=t('Date Modified')?></th>			
			<th ><input type="checkbox" id="mc-cb-all" /></th>			
		</tr>
	
	<?
		
		foreach($cList as $p) { ?>
			<tr class="active">
			<td><?=$p->getCollectionID()?></td>
			<td><a href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$p->getCollectionID()?>" target="_blank"><?=$p->getCollectionName()?></a></td>
			<td ><?=$p->getCollectionDateAdded(DATE_APP_GENERIC_MDY, 'user')?></td>
			<td ><? if ($b->isAlias($p)) { ?> <input type="hidden" name="checkedCIDs[]" value="<?=$p->getCollectionID()?>" /><? } ?><?=$p->getCollectionDateLastModified(DATE_APP_GENERIC_MDY, 'user')?></td>
			<td ><input class="mc-cb" type="checkbox" name="cIDs[]" value="<?=$p->getCollectionID()?>" <? if ($b->isAlias($p)) { ?> checked <? } ?> /></td>
			</tr>
		
		<? } ?>
	
		</table>
		
	<? } ?>
	
	<div class="dialog-buttons">
	<a href="#" class="ccm-dialog-close pull-left btn"><?=t('Cancel')?></a>
	<a href="javascript:void(0)" onclick="$('#ccmBlockMasterCollectionForm').submit()" class="btn btn-primary pull-right"><?=t('Save')?></a>
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
	$('#ccmBlockMasterCollectionForm').concreteAjaxForm();

});

</script>
</form>
</div>