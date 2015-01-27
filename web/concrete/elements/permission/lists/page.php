<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $cat = PermissionKeyCategory::getByHandle('page');?>

<table class="ccm-permission-grid table table-striped">
    <?
    $permissions = PermissionKey::getList('page');
    foreach($permissions as $pk) {
        $pk->setPermissionObject($page);
        ?>
        <tr>
            <td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?=$pk->getPermissionKeyID()?>"><strong><? if ($editPermissions) { ?><a dialog-title="<?=$pk->getPermissionKeyDisplayName()?>" data-pkID="<?=$pk->getPermissionKeyID()?>" data-paID="<?=$pk->getPermissionAccessID()?>" onclick="ccm_PagePermissionLaunchDialog(this)" href="javascript:void(0)"><? } ?><?=$pk->getPermissionKeyDisplayName()?><? if ($editPermissions) { ?></a><? } ?></strong></td>
            <td id="ccm-permission-grid-cell-<?=$pk->getPermissionKeyID()?>" <? if ($editPermissions) { ?>class="ccm-permission-grid-cell"<? } ?>><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
        </tr>
    <? } ?>
    <? if ($editPermissions) { ?>
        <tr>
            <td class="ccm-permission-grid-name" ></td>
            <td>
                <?=Loader::element('permission/clipboard', array('pkCategory' => $cat))?>
            </td>
        </tr>
    <? } ?>
</table>



	<script type="text/javascript">
        ccm_PagePermissionLaunchDialog = function(link) {
            var dupe = $(link).attr('data-duplicate');
            if (dupe != 1) {
                dupe = 0;
            }
            jQuery.fn.dialog.open({
                title: $(link).attr('dialog-title'),
                href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?cID=<?=$page->getCollectionID()?>&ctask=set_advanced_permissions&duplicate=' + dupe + '&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
                modal: false,
                width: 500,
                height: 380
            });
        }
	</script>
