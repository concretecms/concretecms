<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-dashboard-header-buttons btn-group">
    <a href="<?=URL::to('/dashboard/express/entries', $entity->getID())?>" class="btn btn-default"><?=t("Back to List")?></a>
    <button type="button" class="btn btn-danger" data-dialog="delete-entry"><?= t('Delete %s', $entity->getName()) ?></button>
</div>

<div style="display: none">
    <div id="ccm-dialog-delete-entry" class="ccm-ui">
        <form method="post" action="<?=$view->action('delete_entry', $entity->getID())?>">
            <?=Core::make("token")->output('delete_entry')?>
            <input type="hidden" name="entry_id" value="<?=$entry->getID()?>">
            <p><?=t('Are you sure you want to delete this entry? This cannot be undone.')?></p>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-danger pull-right" onclick="$('#ccm-dialog-delete-entry form').submit()"><?=t('Delete Entry')?></button>
            </div>
        </form>
    </div>

</div>

<?php

if (is_object($expressForm)) {
    ?>


    <?php
        print $renderer->render($expressForm, $entry);
    ?>

<?php } ?>

<script type="text/javascript">
    $(function() {
        $('[data-dialog]').on('click', function() {
            var $element = $('#ccm-dialog-' + $(this).attr('data-dialog'));
            if ($(this).attr('data-dialog-title')) {
                var title = $(this).attr('data-dialog-title');
            } else {
                var title = $(this).text();
            }
            jQuery.fn.dialog.open({
                element: $element,
                modal: true,
                width: 320,
                title: title,
                height: 'auto'
            });
        });
    });
</script>