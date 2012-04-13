<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
<? 

Loader::model('collection_attributes');
Loader::model('collection_types');
$dh = Loader::helper('date');

if ($c->isAlias() || $c->getCollectionPointerExternalLink() != '') {

if ($c->getCollectionPointerExternalLink() != '') {
	$cID = $c->getCollectionID();
} else {
	$cID = $c->getCollectionPointerOriginalID();
}

?>

<script type="text/javascript">
$(function() {
	$("#ccm-delete-external-link-form").ajaxForm({
		type: 'POST',
		iframe: true,
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			var r = eval('(' + r + ')');
			if (r != null && r.rel == 'SITEMAP') {
				jQuery.fn.dialog.hideLoader();
				jQuery.fn.dialog.closeTop();
				<? if ($_REQUEST['display_mode'] == 'explore') { ?>
					ccmSitemapExploreNode('<?=$_REQUEST['instance_id']?>', 'explore', '<?=$_REQUEST['select_mode']?>', resp.cParentID);
				<? } else { ?>
					deleteBranchFade(r.cID);
				<? } ?>
	 			ccmAlert.hud(ccmi18n_sitemap.deletePageSuccessMsg, 2000, 'delete_small', ccmi18n_sitemap.deletePage);
			} else {
				window.location.href = '<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=' + r.cParentID;
			}
		}
	});
});
</script>

	<form class="form-stacked" method="post" id="ccm-delete-external-link-form" action="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$cID?>&<?=Loader::helper('validation/token')->getParameter()?>">		
	
	<?=t('Remove this alias or external link?')?>

	<div class="ccm-buttons dialog-buttons">
	<input type="button" class="btn" value="<?=t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" />
	<a href="javascript:void(0)" onclick="$('#ccm-delete-external-link-form').submit()" class="btn ccm-button-right accept error"><span><?=('Delete')?></span></a>
	</div>	
	<input type="hidden" name="display_mode" value="<?=$_REQUEST['display_mode']?>" />
	<input type="hidden" name="instance_id" value="<?=$_REQUEST['instance_id']?>" />
	<input type="hidden" name="select_mode" value="<?=$_REQUEST['select_mode']?>" />
	<input type="hidden" name="ctask" value="remove-alias" />
	<input type="hidden" name="processCollection" value="1" />
	<input type="hidden" name="rel" value="SITEMAP" />


</form>
<? } ?>
</div>
