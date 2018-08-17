<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<form method="post" action="<?php echo $view->action('submit'); ?>">
    <?php echo $token->output('submit'); ?>

    <div class="form-group">
        <div class="checkbox">
            <label for="enable_api">
                <?php echo $form->checkbox('enable_api', 1, $enable_api) ?>
                <span><?php echo t('Enable API'); ?></span>
            </label>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="btn btn-primary pull-right" type="submit"><?=t("Save")?></button>
        </div>
    </div>
</form>
