<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($cp->canAdminPage()) {
$gArray = array();
$gl = new GroupList($c, false, true);
$gArray = $gl->getGroupList();
?>

<div class="ccm-pane-controls">
<form method="post" id="ccmPermissionsForm" name="ccmPermissionsForm" action="<?=$c->getCollectionAction()?>">
<input type="hidden" name="rel" value="<?=$_REQUEST['rel']?>" />

<div class="clearfix">
<h3><?=t('Who can view this page?')?></h3>

<ul class="inputs-list">

<?

foreach ($gArray as $g) {
?>

<li><label><input type="checkbox" name="readGID[]" value="<?=$g->getGroupID()?>" <? if ($g->canRead()) { ?> checked <? } ?> /> <?=$g->getGroupName()?></label></li>

<? } ?>

</ul>
</div>

<div class="clearfix">

<h3><?=t('Who can edit this page?')?></h3>

<ul class="inputs-list">

<?

foreach ($gArray as $g) {
?>

<li><label><input type="checkbox" name="editGID[]" value="<?=$g->getGroupID()?>" <? if ($g->canWrite()) { ?> checked <? } ?> /> <?=$g->getGroupName()?></label></li>

<? } ?>

</ul>
</div>

<div class="dialog-buttons">
	<a href="javascript:void(0)" onclick="ccm_hidePane()" class="ccm-button-left btn"><?=t('Cancel')?></a>
	<a href="javascript:void(0)" onclick="$('form[name=ccmPermissionsForm]').submit()" class="ccm-button-right btn primary"><?=t('Save')?></a>
</div>	
<input type="hidden" name="update_permissions" value="1" class="accept">
<input type="hidden" name="processCollection" value="1">

<script type="text/javascript">
$(function() {
	$("#ccmPermissionsForm").ajaxForm({
		type: 'POST',
		iframe: true,
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			var r = eval('(' + r + ')');
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();

			if (r != null && r.rel == 'SITEMAP') {
				ccmSitemapHighlightPageLabel(r.cID);
			}
			ccmAlert.hud(ccmi18n_sitemap.setPagePermissionsMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
		}
	});
});
</script>

<div class="ccm-spacer">&nbsp;</div>
</form>
</div>
<? } ?>