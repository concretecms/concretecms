<?php defined('C5_EXECUTE') or die("Access Denied.");

/* @var FileVersion $version */
use Concrete\Core\File\Image\Thumbnail\Type\Version;
?>

<div class="ccm-ui">
    <?php
    $i = 1;

    /* @var Version $type */
    foreach ($types as $type) {
        $width = $type->getWidth();
        $height = $type->getHeight() ? $type->getHeight() : t('Automatic');
        $sizingMode = $type->getSizingModeDisplayName();
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

        <?php if ($i % 3 === 1) { ?>
            <div class="row">
        <?php } ?>
                <div class="col-md-4">
                    <div class="ccm-image-thumbnail-card">
                        <div class="ccm-image-thumbnail-display-name">
                            <h4><?php echo $type->getDisplayName(); ?></h4>
                        </div>
                        <small class="ccm-image-thumbnail-dimensions"><?php echo t('%s x %s dimensions (%s)', $width, $height, $sizingMode); ?></small>
                        <?php if ($fp->canEditFileContents() && $hasFile) { ?>
                            <a href="<?php echo $url . '?' . $query; ?>"
                                dialog-width="90%"
                                dialog-height="70%"
                                class="btn btn-sm btn-default dialog-launch"
                                dialog-title="<?php echo t('Edit Thumbnail Images'); ?>">
                                <?php echo t('Edit Thumbnail'); ?>
                            </a>
                        <?php } ?>
                        <hr class="ccm-image-thumbnail-divider">
                        <div class="ccm-file-manager-image-thumbnail">
                            <?php if ($hasFile) { ?>
                                <img class="ccm-file-manager-image-thumbnail-image"
                                    data-handle='<?php echo $type->getHandle(); ?>'
                                    data-fid="<?php echo $version->getFileID(); ?>"
                                    data-fvid="<?php echo $version->getFileVersionID(); ?>"
                                    style="max-width: 100%"
                                    src="<?php echo $configuration->getPublicURLToFile($thumbnailPath); ?>"/>
                            <?php
                            } else {
                                echo t(
                                    'No thumbnail found. Usually this is because the ' .
                                    'source file is smaller than this thumbnail configuration.');
                            }
                            ?>
                        </div>
                    </div>
                </div>
        <?php if ($i % 3 === 0 || $i === count($types)) { ?>
            </div>
        <?php }

        ++$i;
        ?>

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
