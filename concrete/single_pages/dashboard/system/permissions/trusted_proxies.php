<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Page\View\PageView $view */

/* @var array $trustedIPs */

?>
<form method="post" action="<?= $view->action('save') ?>">
    <?php $token->output('ccm_trusted_proxies_save') ?>

    <div class="form-group">
        <?= $form->label('trustedIPs', t('List of IP address/ranges of our proxy')) ?>
        <?= $form->textarea('trustedIPs', implode("\n", $trustedIPs), ['style' => 'resize:vertical', 'rows' => '10']) ?>
        <div class="text-muted">
            <?= t('Separate IP addresses with spaces or new lines.') ?><br />
            <?= t(
                'Accepted values are single addresses (IPv4 like %1$s, and IPv6 like %2$s) and ranges in subnet format (IPv4 like %3$s, and IPv6 like %4$s).',
                '<code>127.0.0.1</code>',
                '<code>::1</code>',
                '<code>127.0.0.1/24</code>',
                '<code>::1/8</code>'
            ) ?><br />
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-primary" type="submit"><?=t('Save')?></button>
        </div>
    </div>

</form>
