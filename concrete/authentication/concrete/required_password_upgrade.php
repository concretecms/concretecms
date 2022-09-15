<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access denied.');

/**
 * @var Concrete\Core\Error\ErrorList\ErrorList|null $callbackError (may be not set)
 * @var Concrete\Core\Authentication\AuthenticationType $authType
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var string $intro_msg
 */

if (isset($callbackError) && $callbackError->has()) {
    View::element('system_errors', ['format' => 'block', 'error' => $callbackError]);
}
?>
<div class="required-password-upgrade">
    <form method="post"
          action="<?= URL::to('/login', 'callback', $authType->getAuthenticationTypeHandle(), 'required_password_upgrade') ?>">
	    <?php $token->output(); ?>
        <h4><?= t('Required password upgrade') ?></h4>
        <div class="ccm-message"></div>
        <div class="help-block mb-3">
            <?= nl2br(h($intro_msg)) ?>
        </div>
        <div class="d-grid">
            <button name="required-password-upgrade" class="btn btn-primary"><?= t('Reset and Email Password') ?></button>
        </div>
    </form>
</div>
