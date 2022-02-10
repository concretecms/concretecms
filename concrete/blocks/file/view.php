<?php defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

/**
 * @var Concrete\Block\File\Controller $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var bool|null $forceDownload
 */
$forceDownload = $forceDownload ?? false;

$c = Page::getCurrentPage();
$f = $controller->getFileObject();
$fp = new Checker($f);
/** @phpstan-ignore-next-line */
if ($f && $fp->canViewFile()) {
    if ($c instanceof Page) {
        $cID = $c->getCollectionID();
    }
    ?>
    <div class="ccm-block-file">
        <?php /** @phpstan-ignore-next-line */ ?>
        <a href="<?php echo $forceDownload ? $f->getForceDownloadURL() : $f->getDownloadURL(); ?>">
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
