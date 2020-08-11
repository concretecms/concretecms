<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $url = $type->getControllerUrl(); ?>

<script type="text/javascript">
    ccm_choosePermissionAccessEntitySiteGroup = function() {
        $.fn.dialog.open({
            title: '<?=t('Choose Site Group')?>',
            href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/permissions/access/entity/site_group',
            width: 500,
            modal: true,
            height: 350
        });
    }
</script>