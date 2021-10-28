<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var string $http_proxy_host
 * @var string $http_proxy_port
 * @var string $http_proxy_user
 * @var string $http_proxy_pwd
 */

?>

<form method="post" id="proxy-form" action="<?= $view->action('update_proxy'); ?>">
    <?php $token->output('update_proxy'); ?>

    <fieldset>
        <div class="form-group">
            <?= $form->label('http_proxy_host', t('Proxy Host')); ?>
            <?= $form->text('http_proxy_host', $http_proxy_host); ?>
        </div>

        <div class="form-group">
            <?= $form->label('http_proxy_port', t('Proxy Port')); ?>
            <?= $form->text('http_proxy_port', $http_proxy_port); ?>
        </div>

        <div class="form-group">
            <?= $form->label('http_proxy_user', t('Proxy User')); ?>
            <?= $form->text('http_proxy_user', $http_proxy_user); ?>
        </div>

        <div class="form-group">
            <?= $form->label('http_proxy_pwd', t('Proxy Password')); ?>
            <?= $form->text('http_proxy_pwd', $http_proxy_pwd); ?>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?= $interface->submit(t('Save'), 'proxy-form', 'right', 'btn-primary'); ?>
        </div>
    </div>
</form>
