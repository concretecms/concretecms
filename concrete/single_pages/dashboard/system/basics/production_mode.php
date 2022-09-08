<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<form action="<?= $view->action('submit') ?>" method="post">
    <?php
    $token->output('submit');
    ?>
    <fieldset>
        <div class="mb-3">
            <?= $form->label('', t('What kind of site is this copy of Concrete running?')); ?>
            <div class="form-check">
                <?= $form->radio('production_mode', \Concrete\Core\Production\Modes::MODE_DEVELOPMENT, $production_mode); ?>
                <?= $form->label('production_mode1', t('Development Site'), ['class' => 'form-check-label']); ?>
            </div>

            <p class="help-block">
                <?=t('<b>Development</b> sites are typically installed on local machines, and used to test Concrete or develop new features and websites.')?>
            </p>


            <div class="form-check">
                <?= $form->radio('production_mode', \Concrete\Core\Production\Modes::MODE_STAGING, $production_mode); ?>
                <?= $form->label('production_mode2', t('Staging Site'), ['class' => 'form-check-label']); ?>
            </div>

            <p class="help-block">
                <?=t('<b>Staging</b> sites are typically somewhat publicly accessible, and may have live or soon-to-be live content on them. They are not actively serving the public, however.')?>
            </p>


            <div class="form-check">
                <?= $form->radio('production_mode', \Concrete\Core\Production\Modes::MODE_PRODUCTION, $production_mode); ?>
                <?= $form->label('production_mode3', t('Production Site'), ['class' => 'form-check-label']); ?>
            </div>

            <p class="help-block">
                <?=t('<b>Production</b> sites are live and serving content to an audience.')?>
            </p>
        </div>
    </fieldset>

    <hr>

    <fieldset>
        <legend><?=t('Staging Options')?></legend>
        <div class="form-check">
            <?= $form->checkbox('show_notification_to_unregistered_users', 1, $show_notification_to_unregistered_users); ?>
            <?= $form->label('show_notification_to_unregistered_users', t('Show staging notification to unregistered users.'), ['class' => 'form-check-label']); ?>
        </div>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary">
                <?=t('Save')?>
            </button>
        </div>
    </div>
</form>
