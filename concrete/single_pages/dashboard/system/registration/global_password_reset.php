<?php defined('C5_EXECUTE') or die('Access Denied.');

$resetText = tc(/*i18n: a text to be asked to the users to confirm the global password reset operation */'GlobalPasswordReset', 'RESET');

?>
<form id="global-password-reset-form" action="<?= $view->action('reset_passwords') ?>" method="post">
    <?= Core::make('helper/validation/token')->output('global_password_reset_token') ?>

    <div class="row">
        <div class="col-md-12">
            <fieldset>
                <div class="form-group">
                    <p>
                        <?=t('Global Password Reset allows Administrators to force reset all user passwords.')?>
                        <?=t('The system signs out all users, resets their passwords and forces them to choose a new one.')?>
                    </p>
                    <div class="alert alert-info">
                        <strong><?=t('Note:')?></strong> <?=t('If you have overridden the standard concrete5 authentication system or you have created your own, you may not need to reset all passwords.')?>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend><?=t('Edit message')?></legend>
                <div class="form-group">
                    <p>
                        <?=t('This message will be shown to users the next time they log in.')?>
                    </p>
                    <div class="input">
                        <?= $form->textarea('resetMessage', $resetMessage, array('rows' => 4, 'cols' => 10)) ?>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend><?=t('Confirmation')?></legend>
                <div class="form-group">
                    <p>
                        <?=t('Type "%s" in the following box to proceed.', h($resetText))?>
                    </p>
                    <div class="input">
                        <?= $form->text('confirmation') ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="alert alert-danger">
                        <strong><?=t('Warning:')?></strong> <?=t('This action will automatically log out all users (including yourself) and reset their passwords.')?>
                    </div>
                </div>
            </fieldset>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <button type="submit" class="btn btn-danger pull-right disabled" name="global-password-reset-form"><?= t('Reset all passwords') ?></button>
                </div>
            </div>

        </div>
    </div>
</form>

<script type="text/javascript">
    $(function () {
        var disableForm = <?php echo json_encode($disableForm) ?>;
        if (disableForm) {
            $("#global-password-reset-form :input").prop("disabled", true);
        }

        $('input[name=confirmation]').on('keyup', function () {
            if ($(this).val() === <?=json_encode($resetText)?>) {
                $('button[name=global-password-reset-form]').removeClass("disabled");
            } else {
                $('button[name=global-password-reset-form]').addClass("disabled");
            }
        });
    });
</script>