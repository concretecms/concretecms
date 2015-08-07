<?php

use Concrete\Core\File\Image\Thumbnail\Type\Version;

defined('C5_EXECUTE') or die("Access Denied.");
/* @var FileVersion $version */
?>
<div class="ccm-ui">
    <?php
    /* @var Version $type */
    foreach ($types as $type) {
        $width = $type->getWidth();
        $height = $type->getHeight() ? $type->getHeight() : t('Automatic');
        $thumbnailPath = $type->getFilePath($version);
        $location = $version->getFile()->getFileStorageLocationObject();
        $configuration = $location->getConfigurationObject();
        $filesystem = $location->getFileSystemObject();
        $hasFile = $filesystem->has($thumbnailPath);

        $url = URL::to('/ccm/system/dialogs/file/thumbnails/edit');
        $query = http_build_query(array(
            'fID' => $version->getFileID(),
            'fvID' => $version->getFileVersionID(),
            'thumbnail' => $type->getHandle(),
        ));
        ?>
        <h4>
            <?= $type->getDisplayName() ?>
            <small><?= t('%s x %s dimensions', $width, $height) ?></small>
            <?php if ($fp->canEditFileContents() && $hasFile) { ?>
                <a href="<?= $url . '?' . $query ?>"
                   dialog-width="90%"
                   dialog-height="70%"
                   class="pull-right btn btn-sm btn-default dialog-launch"
                   dialog-title="<?= t('Edit Thumbnail Images') ?>">
                    <?= t('Edit Thumbnail') ?>
                </a>
            <?php } ?>
        </h4>
        <hr/>
        <div class="ccm-file-manager-image-thumbnail">
            <?php
            if ($hasFile) {
                ?>
                <img class="ccm-file-manager-image-thumbnail-image"
                     data-handle='<?= $type->getHandle() ?>'
                     data-fid="<?= $version->getFileID() ?>"
                     data-fvid="<?= $version->getFileVersionID() ?>"
                     style="max-width: 100%"
                     src="<?= $configuration->getPublicURLToFile($thumbnailPath) ?>"/>
                <?php
            } else {
                echo t(
                    'No thumbnail found. Usually this is because the ' .
                    'source file is smaller than this thumbnail configuration.');
            }
            ?>
        </div>

    <?php } ?>

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
