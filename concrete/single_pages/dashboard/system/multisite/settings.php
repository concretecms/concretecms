<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<form action="<?php echo $view->action('enable_multisite')?>" method="post">
    <?=$token->output('enable_multisite')?>
    <?php if ($service->isMultisiteEnabled()) { ?>
        <p><?=t('Multiple sites are enabled.')?></p>
        <?php
    } else {
        if ($controller->getTask() == 'multisite_required') { ?>
            <p><?=t('<strong>You must enable multiple site hosting before you can access multiple sites or site types.</strong>')?></p>
        <?php } else { ?>
            <p><?=t('Multiple sites are not currently enabled. Enable them below.')?></p>
        <?php } ?>
        <div class="alert alert-info">
            <?=t('<strong>Note:</strong> Enabling multisite support will create a top-level Sites group, and a top-level folder in the file manager named Shared Files. Once enabled, multisite mode cannot be turned off.')?>
        </div>
        <?php
    } ?>

    <?php if (!$service->isMultisiteEnabled()) { ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="btn btn-primary pull-right" type="submit"><?=t('Enable Multiple Sites')?></button>
            </div>
        </div>

        <?php
    } ?>
</form>