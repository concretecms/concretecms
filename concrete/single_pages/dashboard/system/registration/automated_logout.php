<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Page\View\PageView $view */

/* @var bool $invalidateOnIPMismatch */
/* @var bool $invalidateOnUserAgentMismatch */
/* @var string $trustedProxyUrl */
/* @var string $invalidateAction */
/* @var string $saveAction */
?>
<form action="<?= $saveAction ?>" method="POST">
    <?php $token->output('save_automated_logout') ?>

    <fieldset>
        <legend><?= t('Session Security') ?></legend>
        <div class="form-group">

            <div class="help-block">
                <?= t('These settings help prevent a user from stealing other logged in user sessions. You may want to configure %s"Trusted Proxies"%s instead', '<a href="' . $trustedProxyUrl . '">', '</a>') ?>
            </div>
            <div class="checkbox">
                <label>
                    <?= $form->checkbox('invalidateOnIPMismatch', '1', $invalidateOnIPMismatch) ?>
                    <?= t('Log users out if their IP changes') ?>
            </div>
            <div class="checkbox">
                <label>
                    <?= $form->checkbox('invalidateOnUserAgentMismatch', '1', $invalidateOnUserAgentMismatch) ?>
                    <?= t("Log users out if their browser's user agent changes") ?>
                </label>
            </div>

        </div>
    </fieldset>

    <fieldset>
        <legend><?= t('Invalidate Active Sessions') ?></legend>

        <div class="form-group">
            <p>
                <?=t('Type %s in the following box to proceed.', '<code>invalidate</code>')?>
            </p>
            <div class="input">
                <?= $form->text('confirmation', [
                    'data-submit' => $invalidateAction
                ]) ?>
                <div class="invalid-feedback text-danger help-text">
                    <?= t('Please type %s to proceed.', '<code>invalidate</code>') ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="alert alert-danger">
                <strong><?=t('Warning:')?></strong> <?=t('This action will automatically log out all users (including yourself!)')?>
            </div>
        </div>
    </fieldset>



    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="pull-right">
                <a href="#" class="invalidate-submit btn btn-danger"><?= t('Log out all active users') ?></a>
                <button class="btn btn-primary" type="submit"><?= t('Save') ?></button>
            </div>
        </div>
    </div>
</form>

<script type="application/javascript">
    (function() {
        var confirmation = $('[name="confirmation"]'),
            button = $('.invalidate-submit'),
            invalidFeedback = confirmation.parent().children('.invalid-feedback').slideUp(0);

        // Bind running feedback on the input
        confirmation.keyup(function() {
            if (confirmation.val().toLowerCase() == 'invalidate') {
                confirmation.parent().removeClass('has-warning').addClass('has-success');
            } else {
                confirmation.parent().removeClass('has-success').addClass('has-warning');
            }
        });


        // Bind clicking functionality on the button
        button.click(function() {
            if (confirmation.val().toLowerCase() == 'invalidate') {
                confirmation.parent().find('.invalid-feedback').slideUp().parent().removeClass('has-error');
                window.location = confirmation.data('submit');
            } else {
                confirmation.parent().find('.invalid-feedback').slideDown().parent().addClass('has-error');
            }
        });
    }());
</script>
