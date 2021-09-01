<?php
defined('C5_EXECUTE') or die('Access Denied.');

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

<div id="ccm-block-gallery-<?=$bID?>">
    <div class="ccm-block-gallery">
        <div class="row gx-0">
            <?php
            /** @var File $image */
            foreach ($images as $image) {
                $tag = (new Image($image['file']))->getTag();
                $size = $image['displayChoices']['size']['value'] ?? null;
                $caption = $image['displayChoices']['caption']['value'] ?? null;
                $hoverCaption = $image['displayChoices']['hover_caption']['value'] ?? null;
                $downloadLink = null;
                $fileVersion = $image['file']->getApprovedVersion();
                if ($includeDownloadLink && $fileVersion instanceof Version) {
                    $downloadLink = $fileVersion->getForceDownloadURL();
                }
                ?>
                <a class="col-md-<?php echo $size === 'wide' ? '8' : '4' ?> col-<?php echo $size === 'wide' ? '12' : '6' ?>"
                   href="<?php echo h($image['file']->getThumbnailUrl(null)) ?>" data-gallery-lightbox="true"
                   data-caption="<?=h($caption)?>"
                   data-download-link="<?php echo h($downloadLink); ?>">
                    <div class="ccm-block-gallery-image"><?php echo $tag ?></div>
                    <div class="ccm-block-gallery-image-overlay">
                        <div class="ccm-block-gallery-image-overlay-color"></div>
                        <div class="ccm-block-gallery-image-overlay-text"><?=h($hoverCaption)?></div>
                    </div>
                </a>
                <?php
            }
            ?>
        </div>
    </div>
</div>