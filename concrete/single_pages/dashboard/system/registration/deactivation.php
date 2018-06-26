<?php defined('C5_EXECUTE') or die('Access Denied.');

$resetText = tc(/*i18n: a text to be asked to the users to confirm the global password reset operation */'GlobalPasswordReset', 'RESET');

?>
<form action="<?= $view->action('update') ?>" method="post">
    <?= $token->output('update') ?>

    <div class="row">
        <div class="col-md-12">
            <fieldset>
                <legend><?=t('Inactive User Error Message')?></legend>
                <div class="form-group">
                    <p>
                        <?=t('This message will be shown to inactive users when they attempt to login.')?>
                    </p>
                    <div class="input">
                        <?= $form->textarea('inactiveMessage', $inactiveMessage, array('rows' => 4, 'cols' => 10)) ?>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend><?=t('Automatic User Deactivation')?></legend>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <?=$form->checkbox('enableAutomaticUserDeactivation', 1, $enableAutomaticUserDeactivation)?>
                            <?=t('Automatically deactivate users when they have not logged in for awhile. Users will need to be manually reactivated.')?>
                        </label>
                    </div>
                </div>
                <div class="form-group" data-group="user-deactivation-days">
                    <label class="control-label"><?=t('Threshold')?></label>
                    <div class="form-inline">
                        <p><?=t('Deactivate users when they have not logged in for %s days',
                            $form->number('userDeactivationDays', $userDeactivationDays, ['style' => 'width: 100px'])
                        )?></p>
                </div>

                <div class="help-block">
                    <?=t('Note: You will need to run the "Deactivate Users" job in order for these deactivations to actually occur.')?>
                </div>
            </fieldset>


            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <button type="submit" class="btn btn-primary pull-right"><?= t('Save') ?></button>
                </div>
            </div>

        </div>
    </div>

</form>

<script type="text/javascript">
    $('input[name=enableAutomaticUserDeactivation]').on('change', function() {
        if ($(this).is(':checked')) {
            $('div[data-group=user-deactivation-days]').show();
        } else {
            $('div[data-group=user-deactivation-days]').hide();
        }
    }).trigger('change');
</script>