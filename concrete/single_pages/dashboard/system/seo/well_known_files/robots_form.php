<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var array{id: int, handle: string, canonicalUrl: string, robotsContent: string} $row
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\Controller\DashboardPageController $controller
 */

$sitemapUrl = rtrim($row['canonicalUrl'], '/') . '/sitemap.xml';
$placeholder = "User-agent: *\nDisallow:\n\nSitemap: {$sitemapUrl}\n";
?>
<form method="post" action="<?= h($controller->action('save_robots')) ?>" class="ccm-well-known-editor">
    <?php $token->output('save_robots_txt') ?>
    <input type="hidden" name="siteID" value="<?= (int) $row['id'] ?>">
    <div class="form-group">
        <textarea class="form-control font-monospace" name="content" rows="12" spellcheck="false"
                  placeholder="<?= h($placeholder) ?>"><?= h($row['robotsContent']) ?></textarea>
    </div>
    <div class="mt-3 text-end">
        <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
    </div>
</form>
