<?php

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Entity\File\Version $version */
/* @var Concrete\Core\File\Image\Thumbnail\Type\Version[] $types */
?>
<div class="ccm-ui">
    <?php
    $file = $version->getFile();
    $imageWidth = (int) $version->getAttribute('width');
    $imageHeight = (int) $version->getAttribute('height');
    $location = $version->getFile()->getFileStorageLocationObject();
    $configuration = $location->getConfigurationObject();
    $filesystem = $location->getFileSystemObject();
    $url = URL::to('/ccm/system/dialogs/file/thumbnails/edit');
    $i = 1;
    foreach ($types as $type) {
        $width = (int) $type->getWidth() ?: t('Automatic');
        $height = (int) $type->getHeight() ?: t('Automatic');
        $sizingMode = $type->getSizingModeDisplayName();
        $thumbnailPath = $type->getFilePath($version);
        $hasFile = $filesystem->has($thumbnailPath);
        $shouldHaveFile = $type->shouldExistFor($imageWidth, $imageHeight, $file);
        $query = http_build_query([
            'fID' => $version->getFileID(),
            'fvID' => $version->getFileVersionID(),
            'thumbnail' => $type->getHandle(),
        ]);
        if ($i % 3 === 1) {
            ?><div class="row"><?php
        }
        ?>
        <div class="col-md-4">
            <div class="ccm-image-thumbnail-card">
                <div class="ccm-image-thumbnail-display-name">
                    <h4><?php echo $type->getDisplayName(); ?></h4>
                </div>
                <small class="ccm-image-thumbnail-dimensions"><?= t('%s x %s dimensions (%s)', $width, $height, $sizingMode) ?></small>
                <?php
                if ($fp->canEditFileContents() && $hasFile) {
                    ?>
                    <a href="<?= $url . '?' . $query ?>"
                        dialog-width="90%"
                        dialog-height="70%"
                        class="btn btn-sm btn-default dialog-launch"
                        dialog-title="<?= t('Edit Thumbnail Images') ?>"
                    >
                        <?= t('Edit Thumbnail') ?>
                    </a>
                    <?php
                    }
                ?>
                <hr class="ccm-image-thumbnail-divider" />
                <div class="ccm-file-manager-image-thumbnail">
                    <?php
                    if ($hasFile) {
                        ?>
                        <img class="ccm-file-manager-image-thumbnail-image"
                            data-handle="<?= $type->getHandle() ?>"
                            data-fid="<?= $version->getFileID() ?>"
                            data-fvid="<?= $version->getFileVersionID() ?>"
                            style="max-width: 100%"
                            src="<?= $configuration->getPublicURLToFile($thumbnailPath) ?>"
                        />
                        <?php
                    } elseif ($shouldHaveFile) {
                        echo t('Thumbnail not found.');
                    } else {
                        echo t('Thumbnail not to be generated for this file.');
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        if ($i % 3 === 0 || $i === count($types)) {
            ?></div><?php
        }
        ++$i;
    }
    ?>
    <script>
        (function() {
            Concrete.event.unbind('ImageEditorDidSave.thumbnails');
            var thumbnails = $('img.ccm-file-manager-image-thumbnail-image');
            Concrete.event.bind('ImageEditorDidSave.thumbnails', function(event, data) {
                if (data.isThumbnail) {
                    var thumbnail = thumbnails.filter('[data-handle=' + data.handle + '][data-fid=' + data.fID + '][data-fvid=' + data.fvID + ']').get(0);
                    thumbnail.src = data.imgData;
                    $.fn.dialog.closeTop();
                }
            });
        }());
    </script>
</div>
