<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var array{id: int, handle: string, name: string, canonicalUrl: string, llmsContent: string} $row
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\Controller\DashboardPageController $controller
 */

$placeholder = "# {$row['name']}

> Brief description of your site for AI systems.

## Pages

- [Home]({$row['canonicalUrl']}/)

## Allowed

- Indexing for AI training and retrieval
";
?>
<form method="post" action="<?= h($controller->action('save_llms')) ?>" class="ccm-well-known-editor">
    <?php $token->output('save_llms_txt') ?>
    <input type="hidden" name="siteID" value="<?= (int) $row['id'] ?>">
    <div class="form-group">
        <textarea class="form-control font-monospace" name="content" rows="12" spellcheck="false"
                  placeholder="<?= h($placeholder) ?>"><?= h($row['llmsContent']) ?></textarea>
    </div>
    <div class="mt-3 text-end">
        <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
    </div>
</form>
