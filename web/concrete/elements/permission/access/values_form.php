<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<form id="ccm-permission-access-values-form" style="display: none">
<? foreach($permissions as $pk) {  ?>
	<input type="hidden" name="pkID[<?=$pk->getPermissionKeyID()?>]" value="<?=$pk->getPermissionAccessID()?>" />
<? } ?>
</form>