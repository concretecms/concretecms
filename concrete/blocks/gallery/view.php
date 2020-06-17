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

<div class="d-flex flex-wrap">
    <?php
    /** @var \Concrete\Core\Entity\File\File $image */
    foreach ($images as $image) {
        $tag = (new \Concrete\Core\Html\Image($image['file']))->getTag();
        $tag->addClass('w-100 h-auto')
        ?>
        <div class="px-2 pb-4" style="width: calc(100% / 3)">
            <?= $tag ?>
        </div>
        <?php
    }
    ?>
</div>
