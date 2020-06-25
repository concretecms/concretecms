<?php
$page = $controller->getCollectionObject();
$images = $images ?? [];

if (!$images && $page && $page->isEditMode()) {
    ?>
    <div class="ccm-edit-mode-disabled-item"><?=t('Empty Gallery Block.')?></div>
    <?php

    // Stop outputting
    return;
}
?>

<div class="ccm-gallery-container ccm-gallery-<?= $bID ?>">
    <?php
    /** @var \Concrete\Core\Entity\File\File $image */
    foreach ($images as $image) {
        $tag = (new \Concrete\Core\Html\Image($image['file']))->getTag();
        $tag->addClass('gallery-w-100 gallery-h-auto');
        $size = $image['displayChoices']['size']['value'] ?? null;
        ?>
        <div class="<?= $size === 'wide' ? 'gallery-w-100' : 'gallery-w-50' ?>" href="<?= $image['file']->getThumbnailUrl(null) ?>" data-magnific="true">
            <div class="gallery-16-9">
                <?= $tag ?>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<script>
    $(function() {
        $('.ccm-gallery-<?= $bID ?> [data-magnific=true]').magnificPopup({
            type: 'image',
            gallery: {
                enabled: true
            }
        });
    })
</script>
