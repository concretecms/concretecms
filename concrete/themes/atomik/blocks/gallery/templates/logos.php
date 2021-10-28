<?php

/**
 * @project:   ConcreteCMS Theme
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Block\Gallery\Controller;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Html\Image;

/** @var Controller $controller */
/** @var bool $includeDownloadLink */
/** @var int $bID */

$page = $controller->getCollectionObject();
$images = $images ?? [];

if (!$images && $page && $page->isEditMode()) { ?>
    <div class="ccm-edit-mode-disabled-item">
        <?php echo t('Empty Gallery Block.') ?>
    </div>
    <?php

    // Stop outputting
    return;
}
?>

<div class="ccm-block-gallery-logos">
    <?php
    /** @var File $image */
    foreach ($images as $image) {
        $tag = (new Image($image['file']))->getTag();
        $tag->addClass('gallery-w-100 gallery-h-auto');
        $size = $image['displayChoices']['size']['value'] ?? null;
        $downloadLink = null;
        $fileVersion = $image['file']->getApprovedVersion();
        if ($includeDownloadLink && $fileVersion instanceof Version) {
            $downloadLink = $fileVersion->getForceDownloadURL();
        }
        ?>
        <div class="logo">
            <?php echo $tag ?>
        </div>
    <?php } ?>
</div>