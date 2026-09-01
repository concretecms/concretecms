<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var string $content Current llms.txt content for the site being edited
 * @var string $siteName Name of the site being edited
 * @var string $canonicalUrl Canonical URL of the site being edited
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\Controller\DashboardSitePageController $controller
 */

$placeholder = "# {$siteName}

> Brief description of your site for AI systems.

## Pages

- [Home]({$canonicalUrl}/)

## Allowed

- Indexing for AI training and retrieval
";
?>
<form method="post" action="<?= h($controller->action('save_llms')) ?>" class="ccm-well-known-editor">
    <?php $token->output('save_llms_txt') ?>
    <div class="form-group">
        <textarea class="form-control font-monospace" name="content" rows="12" spellcheck="false"
                  placeholder="<?= h($placeholder) ?>"><?= h($content) ?></textarea>
    </div>
    <div class="mt-3 text-end">
        <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
    </div>
</form>
