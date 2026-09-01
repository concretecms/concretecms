<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var string $content Current humans.txt content for the site being edited
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\Controller\DashboardSitePageController $controller
 */

$placeholder = "/* TEAM */
Name: Your Name
Role: Developer
Location: City, Country

/* SITE */
Last update: " . date('Y-m-d') . "
Language: English
";
?>
<form method="post" action="<?= h($controller->action('save_humans')) ?>" class="ccm-well-known-editor">
    <?php $token->output('save_humans_txt') ?>
    <div class="form-group">
        <textarea class="form-control font-monospace" name="content" rows="12" spellcheck="false"
                  placeholder="<?= h($placeholder) ?>"><?= h($content) ?></textarea>
    </div>
    <div class="mt-3 text-end">
        <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
    </div>
</form>
