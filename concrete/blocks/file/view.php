<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Block\File\Controller $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var bool $forceDownload
 */

$c = Page::getCurrentPage();
$f = $controller->getFileObject();
$fp = new Permissions($f);

if ($f && $fp->canViewFile()) {
    if ($c instanceof Page) {
        $cID = $c->getCollectionID();
    }
    ?>
    <div class="ccm-block-file">
        <a href="<?php echo (!empty($forceDownload)) ? $f->getForceDownloadURL() : $f->getDownloadURL(); ?>">
            <?php echo stripslashes($controller->getLinkText()) ?>
        </a>
    </div>
    <?php
}

if (!$f && $c->isEditMode()) {
    ?>
    <div class="ccm-edit-mode-disabled-item"><?= t('Empty File Block.') ?></div>
    <?php
}
