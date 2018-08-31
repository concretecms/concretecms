<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">
        <a href="<?=URL::to('/dashboard/system/api/settings'); ?>" class="btn btn-default"><?php echo t('Back to Settings'); ?></a>
        <button class="btn btn-danger" data-dialog="delete-client"><?=t("Delete")?></button>
        <a href="<?=URL::to('/dashboard/system/api/integrations', 'edit', $client->getIdentifier()); ?>" class="btn btn-primary"><?php echo t('Edit'); ?></a>
    </div>
</div>

<fieldset>
    <legend><?=t('Integration Details')?></legend>

    <div class="form-group">
        <label class="control-label"><?=t('Name')?></label>
        <div><?=$client->getName()?></div>
    </div>

    <div class="form-group">
        <label class="control-label"><?=t('Client ID')?></label>
        <input type="text" class="form-control" onclick="this.select()" value="<?=$client->getClientKey()?>">
    </div>

    <div class="form-group">
        <label class="control-label"><?=t('Client Secret')?></label>
        <input type="text" class="form-control" onclick="this.select()" value="<?=$client->getClientSecret()?>">
    </div>

</fieldset>

<div style="display: none">
    <div data-dialog-wrapper="delete-client">
        <form method="post" action="<?php echo $view->action('delete'); ?>">
            <?php echo Loader::helper('validation/token')->output('delete'); ?>
            <input type="hidden" name="clientID" value="<?php echo $client->getIdentifier(); ?>">
            <p><?=t('Are you sure you want to delete this credentials set? This cannot be undone.')?></p>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-danger pull-right" onclick="$('div[data-dialog-wrapper=delete-client] form').submit()"><?=t('Delete')?></button>
            </div>
        </form>
    </div>
</div>

