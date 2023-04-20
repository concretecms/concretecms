<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<form action="<?= $view->action('submit') ?>" method="post" data-form="production-mode">
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

    <div class="modal fade" role="dialog" tabindex="-1" id="production-modal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form method="post" action="<?=$view->action('enable_production_mode')?>">
                    <?=$token->output('enable_production_mode')?>
                    <div class="modal-header">
                        <h4 class="modal-title"><?= t('Production Site') ?></h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                    </div>
                    <div class="modal-body">
                        <p><?=t('<b>Production sites</b> are live and serving content to an audience. As such, they should be tested for optimal performance and secure configuration.')?></p>
                        <p><?=t('If you proceed with testing, you will be redirected to the <b>Site Health</b> section of the Dashboard, where the production status report will be run. You may also proceed without testing, although this is not advised.')?></p>
                    </div>
                    <div class="modal-footer d-flex justify-content-between w-100">
                        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary border float-start"><?php echo t('Cancel') ?></button>
                        <div class="ms-auto">
                            <button class="btn btn-danger" name="action" value="skip_tests" type="submit"><?php echo t('Skip Tests') ?></button>
                            <button class="btn btn-primary" name="action" value="run_tests" type="submit"><?php echo t('Run Health Tests') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script type="text/javascript">
$(function() {
    $('form[data-form=production-mode]').on('submit', function(e) {
        const mode = $(this).find('input[name=production_mode]:checked').val()
        if (mode === 'production') {
            e.preventDefault()
            const productionModal = document.getElementById('production-modal');
            const modal = bootstrap.Modal.getOrCreateInstance(productionModal);
            if (modal) {
                modal.show();
            }
        }
    })
});
</script>