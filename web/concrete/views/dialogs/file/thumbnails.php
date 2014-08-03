<?php

use Concrete\Core\File\Image\Thumbnail\Type\Version;

defined('C5_EXECUTE') or die("Access Denied.");
/** @var FileVersion $version */
?>
<div class="ccm-ui">

    <?php
    /** @var Version $type */
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
            'thumbnail' => $type->getHandle()
        ));
        ?>
        <h4>
            <?= $type->getName() ?>
            <small><?= t('%s x %s dimensions', $width, $height) ?></small>
            <? if ($fp->canEditFile() && $hasFile) { ?>
                <a href="<?= $url . '?' . $query ?>"
                   dialog-width="90%"
                   dialog-height="70%"
                   class="pull-right btn btn-sm btn-default dialog-launch"
                   dialog-title="<?= t('Edit Thumbnail Images') ?>">
                    <?= t('Edit Thumbnail') ?>
                </a>
            <? } ?>
        </h4>
        <hr/>
        <div class="ccm-file-manager-image-thumbnail">
            <?php
            if ($hasFile) {
                ?>
                <img class="ccm-file-manager-image-thumbnail-image unbound" style="max-width: 100%" src="<?= $configuration->getPublicURLToFile($thumbnailPath) ?>"/>
            <?php
            } else {
                echo t(
                    'No thumbnail found. Usually this is because the ' .
                    'source file is smaller than this thumbnail configuration.');
            }
            ?>
        </div>
        <script>
            (function() {
                var thumbnail = $('img.ccm-file-manager-image-thumbnail-image.unbound').removeClass('unbound');

                Concrete.event.bind('ImageEditorDidSave', function(event, data) {
                    if (data.isThumbnail) {
                        console.log(data, thumbnail);
                        if (data.fID === <?= $version->getFileID() ?> &&
                            data.fvID === <?= $version->getFileVersionID() ?> &&
                            data.handle === '<?= $type->getHandle() ?>') {
                            thumbnail.attr('src', data.url);
                            $.fn.dialog.closeTop();
                        }
                    }
                });
            }());
        </script>

    <? } ?>


</div>
