<?php

defined('C5_EXECUTE') or die('Access Denied.');

?>

<div class="group-tree"></div>

<script type="text/template" id="access-warning-template">
    <div>
        <p>
            <?= t("Moving a group underneath another group will cause all users in the moved group to gain the permissions of the parent group."); ?>
        </p>
        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
            <button class="btn btn-danger float-end accept"><?= t('I understand') ?></button>
        </div>
    </div>
</script>
<script type="text/javascript">
$(function() {
    const parentDragRequest = ConcreteTree.prototype.dragRequest
    ConcreteTree.prototype.dragRequest = function() {
        const me = this
        const params = arguments
        const dialog = $($('#access-warning-template').text())
        dialog.find('button.accept').click(function() {
            parentDragRequest.apply(me, params)
            jQuery.fn.dialog.closeTop()
        })
        jQuery.fn.dialog.open({
            title: "<?= t('Access Warning') ?>",
            element: dialog,
            modal: true,
            width: 500,
            height: 180
        })
    }

    new ConcreteTree($('.group-tree'), {
        treeID: <?= json_encode($tree->getTreeID()) ?>,
    })
})
</script>
