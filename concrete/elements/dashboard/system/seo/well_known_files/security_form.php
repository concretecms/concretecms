<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var string $content Current security.txt content for the site being edited
 * @var string $canonicalUrl Canonical URL of the site being edited
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\Controller\DashboardSitePageController $controller
 */

$canonicalHost = parse_url($canonicalUrl, PHP_URL_HOST) ?: 'example.com';
$expires = date('Y-m-d', strtotime('+1 year')) . 'T00:00:00.000Z';
$placeholder = "Contact: mailto:security@{$canonicalHost}
Expires: {$expires}
Preferred-Languages: en
";
?>
<form method="post" action="<?= h($controller->action('save_security')) ?>" class="ccm-well-known-editor">
    <?php $token->output('save_security_txt') ?>
    <div class="form-group">
        <textarea class="form-control font-monospace" name="content" rows="8" spellcheck="false"
                  placeholder="<?= h($placeholder) ?>"><?= h($content) ?></textarea>
    </div>
    <div class="mt-3 text-end">
        <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
    </div>
</form>
