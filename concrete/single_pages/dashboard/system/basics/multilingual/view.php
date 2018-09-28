<?php
defined('C5_EXECUTE') or die('Access Denied.');

?>
<div class="ccm-dashboard-header-buttons btn-group">
    <a href="<?= $view->action('update') ?>" class="btn btn-default"><?= t('Install/Update Languages') ?></a>
</div>
<?php

if (empty($interfacelocales)) {
    ?>
    <fieldset>
	   <?= t("You don't have any interface languages installed. You must run concrete5 in English.") ?>
    </fieldset>
    <?php 
} else {
    ?>
    <form method="post" action="<?= $view->action('save_interface_language') ?>">
        <fieldset>
            <div class="form-group">
                <?= $form->label('LANGUAGE_CHOOSE_ON_LOGIN', t('Login')) ?>
                <div class="checkbox">
                    <label><?= $form->checkbox('LANGUAGE_CHOOSE_ON_LOGIN', 1, $LANGUAGE_CHOOSE_ON_LOGIN) ?><?= t('Offer choice of language on login.') ?></label>
                </div>
            </div>
            <div class="form-group">
                <?= $form->label('SITE_LOCALE', t('Default Language')) ?>
                <div class="checkbox">
                    <?= $form->select('SITE_LOCALE', $interfacelocales, $SITE_LOCALE) ?>
                </div>
            </div>
            <?php $token->output('save_interface_language') ?>
        </fieldset>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <?= Core::make('helper/concrete/ui')->submit(t('Save'), 'save', 'right', 'btn-primary') ?>
            </div>
        </div>
    </form>
    <?php 
}