<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<form id="global-password-reset-form" action="<?= $view->action('reset_passwords') ?>" method="post">
    <?= Core::make('helper/validation/token')->output('global_password_reset_token') ?>

    <div class="row">
        <div class="col-md-12">
            <fieldset>
                <div class="form-group">
                    <p>
                        Global Password Reset allows Administrators to force reset all user passwords.
                        The system signs out all users, resets their passwords and forces them to choose a new one.
                    </p>
                    <div class="alert alert-info">
                        <strong>Note: </strong>If you have overridden the standard Concrete5 authentication system
                        or you have created your own, you may not need to reset all passwords.
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <div class="form-group">
                    <legend>Edit message</legend>
                    <p>
                        This message will be shown to users the next time they log in.
                    </p>
                    <div class="input">
                        <?= $form->textarea('resetMessage', $resetMessage, array('rows' => 4, 'cols' => 10)) ?>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <div class="form-group">
                    <legend>Confirmation</legend>
                    <p>
                        Type "RESET" in the following box to proceed.
                    </p>
                    <div class="input">
                        <?= $form->text('confirmation') ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="alert alert-danger">
                        <strong>Warning: </strong>This action will automatically log out all users (including yourself)
                        and reset their passwords.
                    </div>
                </div>
            </fieldset>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <button type="submit" class="btn btn-danger pull-right disabled"
                            name="global-password-reset-form"><?= t('Reset all passwords') ?></button>
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
            if ($(this).val() === "RESET") {
                $('button[name=global-password-reset-form]').removeClass("disabled");
            } else {
                $('button[name=global-password-reset-form]').addClass("disabled");
            }
        });
    });
</script>