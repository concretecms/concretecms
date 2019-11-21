<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var string $loginUrl
 * @var string $saveAction
 */

?>
<form action="<?= $saveAction ?>" method="POST">
    <?php $token->output('set-login-url') ?>
    
    <div class="form-group">
        <?= $form->label('loginUrl', t('Relative URL of the login page')) ?>
        <?= $form->text('loginUrl', $loginUrl, ['required' => 'required']) ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="pull-right">
                <button class="btn btn-primary" type="submit"><?= t('Save') ?></button>
            </div>
        </div>
    </div>
    
</form>