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

<div class="ccm-gallery-container ccm-gallery-<?php echo h($bID) ?>">
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
        <div class="<?php echo $size === 'wide' ? 'gallery-w-100' : 'gallery-w-50' ?>"
             href="<?php echo h($image['file']->getThumbnailUrl(null)) ?>" data-magnific="true"
             data-download-link="<?php echo h($downloadLink); ?>">
            <div class="gallery-16-9">
                <?php echo $tag ?>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<script>
    $(function () {
        $('.ccm-gallery-<?php echo $bID ?> [data-magnific=true]').magnificPopup({
            type: 'image',
            gallery: {
                enabled: true
            },
            image: {
                titleSrc: function (item) {
                    var downloadLink = item.el.attr('data-download-link');
                    if (downloadLink.length) {
                        var $a = $("<a></a>");
                        $a.attr("href", downloadLink);
                        $a.attr("target", "_blank");
                        $a.html("<?php echo h(t("Click here to download.")); ?>");
                        return $a.wrap('<div/>').parent().html();
                    }
                }
            }
        });
    })
</script>
