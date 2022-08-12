<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">

    <?php if (count($pages) == 0) {
        ?>
        <?=t("You do not have permission to delete any of the selected pages.");
        ?>
        <?php
    } else {
        ?>

        <?=t('Are you sure you want to delete the following pages?')?><br/><br/>

        <form data-dialog-form-processing="progressive" data-dialog-form-processing-title="<?=t('Delete Pages')?>" data-dialog-form="delete-pages" method="post" action="<?=$controller->action('submit')?>">
            <table border="0" cellspacing="0" cellpadding="0" width="100%" class="table table-striped">
                <tr>
                    <th><?=t('Name')?></th>
                    <th><?=t('Page Type')?></th>
                    <th><?=t('Date Added')?></th>
                    <th><?=t('Author')?></th>
                </tr>

                <?php foreach ($pages as $c) {
                    $cp = new Permissions($c);
                    $c->loadVersionObject();
                    if ($cp->canDeletePage() && $c->getCollectionID() > 1) {
                        ?>

                        <?=$form->hidden('item[]', $c->getCollectionID())?>

                        <tr>
                        <td class="ccm-page-list-name"><?=$c->getCollectionName()?></td>
                        <td><?=$c->getPageTypeName()?></td>
                        <td><?=$dh->formatDateTime($c->getCollectionDatePublic())?></td>
                        <td><?php
                        $ui = UserInfo::getByID($c->getCollectionUserID());
                        if (is_object($ui)) {
                            echo $ui->getUserName();
                        }
                    }
                    ?></td>

                    </tr>

                    <?php
                }
                ?>
            </table>
        </form>


        <div class="dialog-buttons">
            <button class="btn btn-secondary" data-dialog-action="cancel"><?= t('Cancel'); ?></button>
            <button type="button" data-dialog-action="submit" class="btn btn-danger ms-auto"><?=t('Delete')?></button>
        </div>

        <?php

    }
    ?>
</div>

<script type="text/javascript">
    $(function() {
        ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.sitemapBulkDelete');
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess.sitemapBulkDelete', function(e, data) {
            if (data.form == 'delete-pages') {
                jQuery.fn.dialog.closeAll();
                ConcreteEvent.publish('SitemapDeleteRequestComplete');
                ConcreteAlert.notify({message: <?=json_encode(t('Pages deleted successfully.'))?>});
            }
        });
    });
</script>

