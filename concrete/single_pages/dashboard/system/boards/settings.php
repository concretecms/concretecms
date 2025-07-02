<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" action="<?=$view->action('save')?>">
    <?=$token->output('save')?>

    <fieldset>
        <legend><?=t('Reporting'); ?></legend>
        <div class="form-group">
            <div class="form-check">
                <?= $form->checkbox('log_board_instances', 1, $logBoardInstances); ?>
                <?= $form->label('log_board_instances', t('Log Board Instance Generation'), ['class' => 'form-check-label']); ?>
            </div>

            <p class="help-block">
                <?= t('If board instances are logged, they will be stored against the board object and viewable from the Dashboard. This can be useful when trying to determine why boards are not displaying data in the proper way.'); ?>
            </p>
        </div>
    </fieldset>
    <fieldset>
        <legend><?=t('Updates'); ?></legend>
        <div class="form-group">
            <div class="form-check">
                <?= $form->checkbox('automatically_refresh_instances', 1, $automaticallyRefreshInstances); ?>
                <?= $form->label('automatically_refresh_instances', t('Automatically Refresh Instances'), ['class' => 'form-check-label']); ?>
            </div>

            <p class="help-block">
                <?= t('If enabled, automatically update boards when their content items change. Disable if you are using the console command to do this.'); ?>
            </p>
        </div>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="btn btn-primary float-end" type="submit"><?= t('Save'); ?></button>
        </div>
	</div>

</form>
