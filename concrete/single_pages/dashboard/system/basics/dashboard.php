
<form id="ccm-editor-config" method="post" class="ccm-dashboard-content-form" action="<?= $view->action('submit') ?>">
    <?php $token->output('submit') ?>
    <div>
        <?= $form->label('dashboardMenuID', t('Dashboard Menu')) ?>
        <?= $form->select('dashboardMenuID', $dashboardMenus, $dashboardMenuID) ?>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary" type="submit"><?= t('Save') ?></button>
        </div>
    </div>
</form>