<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var array{id: int, handle: string, name: string, canonicalUrl: string, securityContent: string} $row
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\Controller\DashboardPageController $controller
 */

$canonicalHost = parse_url($row['canonicalUrl'], PHP_URL_HOST) ?: 'example.com';
$expires = date('Y-m-d', strtotime('+1 year')) . 'T00:00:00.000Z';
$placeholder = "Contact: mailto:security@{$canonicalHost}
Expires: {$expires}
Preferred-Languages: en
";
?>
<form method="post" action="<?= h($controller->action('save_security')) ?>" class="ccm-well-known-editor">
    <?php $token->output('save_security_txt') ?>
    <input type="hidden" name="siteID" value="<?= (int) $row['id'] ?>">
    <div class="form-group">
        <textarea class="form-control font-monospace" name="content" rows="8" spellcheck="false"
                  placeholder="<?= h($placeholder) ?>"><?= h($row['securityContent']) ?></textarea>
    </div>
    <div class="mt-3 text-end">
        <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
    </div>
</form>
