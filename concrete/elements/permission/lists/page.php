<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php $cat = PermissionKeyCategory::getByHandle('page');?>

<table class="ccm-permission-grid table table-striped">
    <?php
    $permissions = PermissionKey::getList('page');
    foreach ($permissions as $pk) {
        if (isset($pages)) {
            $page = $pages[0];
            $pk->setPermissionObject($page);
            $pk->setMultiplePageArray($pages);
        } else {
            $pk->setPermissionObject($page);
        }
        ?>
        <tr>
            <td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?=$pk->getPermissionKeyID()?>"><strong><?php if ($editPermissions) {
    ?><a dialog-title="<?=$pk->getPermissionKeyDisplayName()?>" data-pkID="<?=$pk->getPermissionKeyID()?>" data-paID="<?=$pk->getPermissionAccessID()?>" onclick="ccm_PagePermissionLaunchDialog(this)" href="javascript:void(0)"><?php 
}
        ?><?=$pk->getPermissionKeyDisplayName()?><?php if ($editPermissions) {
    ?></a><?php 
}
        ?></strong></td>
            <td id="ccm-permission-grid-cell-<?=$pk->getPermissionKeyID()?>" <?php if ($editPermissions) {
    ?>class="ccm-permission-grid-cell"<?php 
}
        ?>><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
        </tr>
    <?php 
    } ?>
    <?php if ($editPermissions) {
    ?>
        <tr>
            <td class="ccm-permission-grid-name" ></td>
            <td>
                <?=Loader::element('permission/clipboard', array('pkCategory' => $cat))?>
            </td>
        </tr>
    <?php 
} ?>
</table>



	<script type="text/javascript">
        ccm_PagePermissionLaunchDialog = function(link) {
            var dupe = $(link).attr('data-duplicate');
            if (dupe != 1) {
                dupe = 0;
            }
            jQuery.fn.dialog.open({
                title: $(link).attr('dialog-title'),
                href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/permissions/collection?cID=<?= $page->getCollectionID() ?>&duplicate=' + dupe + '&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
                modal: true,
                width: 500,
                height: 380
            });
        }
	</script>
