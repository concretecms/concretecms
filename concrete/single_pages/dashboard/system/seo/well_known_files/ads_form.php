<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var array{id: int, handle: string, adsContent: string} $row
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\Controller\DashboardPageController $controller
 */

$placeholder = "# Authorized Digital Sellers — https://iabtechlab.com/ads-txt/
# Format: SSP/Exchange Domain, Publisher ID, Relationship, Tag ID (optional)
# Example:
google.com, pub-0000000000000000, DIRECT, f08c47fec0942fa0
";
?>
<form method="post" action="<?= h($controller->action('save_ads')) ?>" class="ccm-well-known-editor">
    <?php $token->output('save_ads_txt') ?>
    <input type="hidden" name="siteID" value="<?= (int) $row['id'] ?>">
    <div class="form-group">
        <textarea class="form-control font-monospace" name="content" rows="12" spellcheck="false"
                  placeholder="<?= h($placeholder) ?>"><?= h($row['adsContent']) ?></textarea>
    </div>
    <div class="mt-3 text-end">
        <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
    </div>
</form>
