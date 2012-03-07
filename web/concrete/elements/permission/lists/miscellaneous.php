<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<table class="ccm-permission-grid">
<?
$permissions = PermissionKey::getList('sitemap');
$permissions = array_merge($permissions, PermissionKey::getList('marketplace_newsflow'));
$permissions = array_merge($permissions, PermissionKey::getList('admin'));

foreach($permissions as $pk) { 
	?>
	<tr>
	<td class="ccm-permission-grid-name"><strong><a dialog-width="500" dialog-height="380" dialog-on-destroy="ccm_refreshMiscellaneousPermissions()" class="dialog-launch" dialog-title="<?=$pk->getPermissionKeyName()?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/miscellaneous?pkID=<?=$pk->getPermissionKeyID()?>"><?=$pk->getPermissionKeyName()?></a></td>
	<td><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<? } ?>
</table>

	<script type="text/javascript">
	$(function() {
		$('.dialog-launch').dialog();
	});
	ccm_refreshMiscellaneousPermissions = function() {
		window.location.reload();
	}
	</script>