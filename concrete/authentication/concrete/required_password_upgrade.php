<?php

defined('C5_EXECUTE') or die('Access denied.');

/**
 * @var Concrete\Core\Authentication\AuthenticationType $authType
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var string|null $intro_msg (may be not set)
 */
?>

<div class="required-password-upgrade">
    <form method="post"
          action="<?= URL::to('/login', 'callback', $authType->getAuthenticationTypeHandle(), 'required_password_upgrade') ?>">
	    <?php $token->output(); ?>
        <h4><?= t('Required password upgrade') ?></h4>
        <div class="ccm-message"></div>
        <div class='help-block'>
            <?= $intro_msg ?? t('Your user account is being upgraded and requires a new password. Please enter your email address below to create this now.') ?>
        </div>
        <div class="form-group">
            <input name="uEmail" type="email" placeholder="<?= t('Email Address') ?>" class="form-control"/>
        </div>
        <div class="d-grid">
            <button name="required-password-upgrade" class="btn btn-primary"><?= t('Reset and Email Password') ?></button>
        </div>
    </form>
</div>
