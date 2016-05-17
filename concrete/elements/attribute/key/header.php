<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div style="display: none">
<div id="ccm-dialog-delete-attribute" class="ccm-ui">
    <form method="post" action="<?= $deleteAction ?>">
        <?=Core::make("token")->output('delete_attribute')?>
        <input type="hidden" name="id" value="<?=$key->getAttributeKeyID()?>">
        <p><?=t('Are you sure you want to delete this attribute? This cannot be undone.')?></p>
        <div class="dialog-buttons">
            <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
            <button class="btn btn-danger pull-right" onclick="$('#ccm-dialog-delete-attribute form').submit()"><?=t('Delete Attribute')?></button>
        </div>
    </form>
</div>
</div>


<div class="ccm-dashboard-header-buttons">
    <button type="button" class="btn btn-danger" data-action="delete-attribute"><?= t('Delete Attribute') ?></button>
</div>

<script type="text/javascript">
    $(function() {
        $('button[data-action=delete-attribute]').on('click', function() {
            var $element = $('#ccm-dialog-delete-attribute');
            jQuery.fn.dialog.open({
                element: $element,
                modal: true,
                width: 320,
                title: '<?=t('Delete Attribute')?>',
                height: 'auto'
            });
        });
    });
</script>
