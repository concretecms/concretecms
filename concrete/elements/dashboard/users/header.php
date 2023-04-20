<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\View\BasicFileView $view
 * @var Concrete\Core\Validation\CSRF\Token $token_validator
 * @var Concrete\Core\User\UserInfo $user
 * @var bool $canActivateUser
 * @var bool $canSignInAsUser
 * @var bool $canResetPassword
 * @var bool $canDeleteUser
 * @var bool $validateEmailRegistration
 * @var string[] $workflowRequestActions
 */
?>

<div class="btn-group">
    <?php
    if ($validateEmailRegistration && $canActivateUser) {
        if ($user->isValidated() < 1) {
            ?>
            <div class="btn-group">
                <button type="button" class="btn dropdown-toggle btn-secondary" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="bstb-undefined">
                    <span><?= t('Validate') ?></span> <span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    <a href="<?= $view->action('update_status', 'validate', $user->getUserID(), $token_validator->generate()) ?>" class="dropdown-item"><?= t('Mark Email As Valid') ?></a>
                    <a href="<?= $view->action('update_status', 'send_email_validation', $user->getUserID(), $token_validator->generate()) ?>" class="dropdown-item"><?= t('Re-Send Email Validation') ?></a>
                </div>
            </div>
            <?php
        }
    }
    if ($canActivateUser) {
        if ($user->isActive()) {
            if (!in_array('deactivate', $workflowRequestActions, true)) {
                ?>
                <button type="button" name="task" data-bs-toggle="modal" data-bs-target="#deactivate-user-modal" class="btn btn-secondary"><?= t('Deactivate') ?></button>
                <?php
            }
        } else {
            if (!in_array('activate', $workflowRequestActions, true) && !in_array('register_activate', $workflowRequestActions, true)) {
                ?>
                <button type="button" name="task" data-bs-toggle="modal" data-bs-target="#activate-user-modal" class="btn btn-secondary"><?= t('Activate') ?></button>
                <?php
            }
        }
    }
    if ($canSignInAsUser) {
        ?>
        <button type="button" data-bs-toggle="modal" data-bs-target="#sudo-user-modal" class="btn btn-secondary"><?= t('Sign in As User') ?></button>
        <?php
    }
    if ($canResetPassword) {
        ?>
        <button type="button" data-bs-toggle="modal" data-bs-target="#user-password-reset-modal" class="btn btn-warning"><?= t('Reset Password') ?></button>
        <?php
    }
    if ($canDeleteUser) {
        ?>
        <button type="button" data-bs-toggle="modal" data-bs-target="#delete-user-modal" class="btn btn-danger"><?= t('Delete') ?></button>
        <?php
    }
    ?>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="deactivate-user-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= $view->action('update_status', 'deactivate', $user->getUserID()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><?= t('Deactivate User') ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <?= $token_validator->output() ?>
                    <?= t('Are you sure you want to deactivate this user?') ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('Close') ?></button>
                    <button type="submit" class="btn btn-danger"><?= t('Deactivate User') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="activate-user-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= $view->action('update_status', 'activate', $user->getUserID()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><?= t('Deactivate User') ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <?= $token_validator->output() ?>
                    <?= t('Click below to activate this user.') ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('Close') ?></button>
                    <button type="submit" class="btn btn-primary"><?= t('Activate User') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="sudo-user-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= $view->action('update_status', 'sudo', $user->getUserID()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><?= t('Sign In As User') ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <?= $token_validator->output() ?>
                    <?= t('This will end your current session and sign you in as %s', $user->getUserName()) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('Close') ?></button>
                    <button type="submit" class="btn btn-primary"><?= t('Sign In') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="user-password-reset-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= $view->action('update_status', 'reset_password', $user->getUserID()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><?= t('Password Reset') ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <?= $token_validator->output() ?>
                    <?= t('Are you sure you force the user to change their password at next login?') ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('Close') ?></button>
                    <button type="submit" class="btn btn-warning"><?= t('Reset') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-user-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= $view->action('update_status', 'delete', $user->getUserID()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><?= t('Delete User') ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <?= $token_validator->output() ?>
                    <?= t('Are you sure you want to permanently remove this user?') ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('Close') ?></button>
                    <button type="submit" class="btn btn-danger"><?= t('Delete') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
