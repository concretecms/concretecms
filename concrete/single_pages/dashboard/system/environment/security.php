<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var \Concrete\Core\Page\View\PageView $view */
/** @var \Concrete\Core\Form\Service\Form $form */
/** @var \Concrete\Core\Validation\CSRF\Token $token */

$content_security_policy = $content_security_policy ?? null;
$strict_transport_security = $strict_transport_security ?? null;
$x_frame_options = $x_frame_options ?? null;
?>
<div class="alert alert-warning">
    <?php echo t('Changing these values may block users from accessing your site. Please change it at your own risk.'); ?>
</div>
<form action="<?= $view->action('submit') ?>" method="post">
    <?php $token->output('update_security_policy'); ?>

    <div class="form-group">
        <?= $form->label('content_security_policy', t('Content Security Policy (CSP)')) ?>
        <?= $form->textarea('content_security_policy', $content_security_policy, ['placeholder' => "default-src 'self'; frame-ancestors 'self'; form-action 'self';"]) ?>
        <div class="help-block">
            <p><?= t('Content Security Policy (CSP) helps mitigate some types of attacks like Cross-Site Scripting (XSS). Please separate policies on different lines if you want to set multiple policies.') ?></p>
            <p><?= t('See also:') ?></p>
            <ul>
                <li><a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy" target="_blank">MDN Web Docs: Content-Security-Policy</a></li>
                <li><a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP" target="_blank">MDN Web Docs: Content Security Policy (CSP)</a></li>
                <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Content_Security_Policy_Cheat_Sheet.html" target="_blank">OWASP Cheat Sheet Series: Content Security Policy Cheat Sheet</a></li>
                <li><a href="https://csp-evaluator.withgoogle.com/" target="_blank">CSP Evaluator</a></li>
            </ul>
        </div>
    </div>

    <div class="form-group">
        <?= $form->label('strict_transport_security', t('Strict-Transport-Security (HSTS)')) ?>
        <?= $form->text('strict_transport_security', $strict_transport_security, ['placeholder' => 'max-age=63072000; includeSubDomains; preload']) ?>
        <div class="help-block">
            <p><?= t('Strict-Transport-Security (HSTS) header informs browsers this site should only be loaded using HTTPS to prevent man-in-the-middle attacks.') ?></p>
            <p><?= t('See also:') ?></p>
            <ul>
                <li><a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security" target="_blank">MDN Web Docs: Strict-Transport-Security</a></li>
                <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Strict_Transport_Security_Cheat_Sheet.html" target="_blank">OWASP Cheat Sheet Series: HTTP Strict Transport Security Cheat Sheet</a></li>
            </ul>
        </div>
    </div>

    <div class="form-group">
        <?= $form->label('x_frame_options', t('X-Frame-Options')) ?>
        <?= $form->text('x_frame_options', $x_frame_options, ['placeholder' => 'SAMEORIGIN']) ?>
        <div class="help-block">
            <p><?= t('X-Frame-Options header informs browsers this site allows or denies to be loaded in a frame, iframe, embed, or object to avoid click-jacking attacks.') ?></p>
            <p><?= t('See also:') ?></p>
            <ul>
                <li><a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options" target="_blank">MDN Web Docs: X-Frame-Options</a></li>
                <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Headers_Cheat_Sheet.html" target="_blank">OWASP Cheat Sheet Series: HTTP Security Response Headers Cheat Sheet</a></li>
            </ul>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" class="btn btn-primary float-end">
                <?php echo t('Save') ?>
            </button>
        </div>
    </div>
</form>
