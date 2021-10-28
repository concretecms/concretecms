<?php

/** @noinspection PhpDeprecationInspection */
/** @noinspection PhpUndefinedMethodInspection */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\File\Image\Thumbnail\ThumbnailPlaceholderService;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\File\Image\Thumbnail\Type\Version;
use Concrete\Core\Entity\File\Version as FileVersion;

/** @var FileVersion $version */
/** @var Version[] $types */
/** @var FilePermissions $fp */

$app = Application::getFacadeApplication();
/** @var ThumbnailPlaceholderService $thumbnailPlaceholderService */
$thumbnailPlaceholderService =$app->make(ThumbnailPlaceholderService::class);
$file = $version->getFile();
$imageWidth = (int)$version->getAttribute('width');
$imageHeight = (int)$version->getAttribute('height');
$location = $version->getFile()->getFileStorageLocationObject();
$configuration = $location->getConfigurationObject();
$filesystem = $location->getFileSystemObject();
$i = 1;

?>

<div class="ccm-ui">
    <?php foreach ($types as $type): ?>
        <?php if ($i % 3 === 1): ?>
            <div class="row">
        <?php endif; ?>
                <div class="col-md-4">
                    <div class="ccm-image-thumbnail-card">
                        <div class="ccm-image-thumbnail-display-name">
                            <h4>
                                <?php echo $type->getDisplayName(); ?>
                            </h4>
                        </div>

                        <small class="ccm-image-thumbnail-dimensions">
                            <?php
                                echo t(
                                    '%s x %s dimensions (%s)',
                                    (int)$type->getWidth() ? : t('Automatic'),
                                    (int)$type->getHeight() ? : t('Automatic'),
                                    $type->getSizingModeDisplayName()
                                );
                            ?>
                        </small>

                        <?php if ($fp->canEditFileContents() && $filesystem->has($type->getFilePath($version))): ?>
                            <!--suppress HtmlUnknownAttribute -->
                            <a
                                href="<?php echo (string)Url::to('/ccm/system/dialogs/file/thumbnails/edit')->setQuery([
                                    'fID' => $version->getFileID(),
                                    'fvID' => $version->getFileVersionID(),
                                    'thumbnail' => $type->getHandle(),
                                ]) ?>"
                               dialog-width="90%"
                               dialog-height="70%"
                               class="btn btn-sm btn-secondary dialog-launch"
                               dialog-title="<?php echo h(t('Edit Thumbnail Images')); ?>">

                                <?php echo t('Edit Thumbnail') ?>
                            </a>
                        <?php endif; ?>

                        <hr class="ccm-image-thumbnail-divider"/>

                        <div class="ccm-file-manager-image-thumbnail">
                            <?php if ($filesystem->has($type->getFilePath($version))): ?>
                                <img class="ccm-file-manager-image-thumbnail-image"
                                     data-handle="<?php echo $type->getHandle() ?>"
                                     data-fid="<?php echo $version->getFileID() ?>"
                                     data-fvid="<?php echo $version->getFileVersionID() ?>"
                                     alt="<?php echo h($version->getFileName()) ?>"
                                     src="<?php echo $configuration->getPublicURLToFile($type->getFilePath($version)) ?>"
                                />
                            <?php elseif ($type->shouldExistFor($imageWidth, $imageHeight, $file)): ?>
                                <?php echo $thumbnailPlaceholderService->getThumbnailPlaceholder(
                                        $version,
                                        $type,
                                        [
                                                "class" => "ccm-file-manager-image-thumbnail-image"
                                        ]
                                ); ?>
                            <?php else: ?>
                                <?php echo t('Thumbnail not to be generated for this file.'); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

        <?php if ($i % 3 === 0 || $i === count($types)): ?>
            </div>
        <?php endif; ?>

        <?php ++$i; ?>
    <?php endforeach; ?>

    <style type="text/css">
        .ccm-file-manager-image-thumbnail-image {
            max-width: 100%;
        }
    </style>

    <!--suppress JSUnresolvedVariable -->
    <script>
        (function($) {
            (function () {
                Concrete.event.unbind('ImageEditorDidSave.thumbnails');

                let $thumbnails = $('img.ccm-file-manager-image-thumbnail-image');

                Concrete.event.bind('ImageEditorDidSave.thumbnails', function (event, data) {
                    if (data.isThumbnail) {
                        let $thumbnail = $thumbnails.filter('[data-handle=' + data.handle + '][data-fid=' + data.fID + '][data-fvid=' + data.fvID + ']');
                        $thumbnail.attr("src", data.imgData);
                        $.fn.dialog.closeTop();
                    }
                });
            }());
        })(jQuery);
    </script>
</div>
