<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var Concrete\Block\Video\Controller $controller */
/** @var \Concrete\Core\Form\Service\Form $form */

/** @var \Concrete\Core\Application\Service\FileManager $fileManager */
$fileManager = app(\Concrete\Core\Application\Service\FileManager::class);

/** @var \Concrete\Core\Entity\File\File|null $webm */
$webm = $controller->getWebmFileID() > 0 ? $controller->getWebmFileObject() : null;
/** @var \Concrete\Core\Entity\File\File|null $ogg */
$ogg = $controller->getOggFileID() > 0 ? $controller->getOggFileObject() : null;
/** @var \Concrete\Core\Entity\File\File|null $mp4 */
$mp4 = $controller->getMp4FileID() > 0 ? $controller->getMp4FileObject() : null;
/** @var \Concrete\Core\Entity\File\File|null $poster */
$poster = $controller->getPosterFileID() > 0 ? $controller->getPosterFileObject() : null;

$videoSize = $videoSize ?? 0;
$width = $width ?? null;

?>

<fieldset>
    <legend>
        <?php echo t('Video Files'); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label('posterfID', t('Video Placeholder Image (Optional)')); ?>
        <?php echo $fileManager->image('ccm-b-poster-file', 'posterfID', t('Choose Video Placeholder Image'), $poster); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('webmfID', t('WebM')); ?>
        <?php echo $fileManager->video('ccm-b-webm-file', 'webmfID', t('Choose WebM Video File'), $webm); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('oggfID', t('Ogg')); ?>
        <?php echo $fileManager->video('ccm-b-ogg-file', 'oggfID', t('Choose Ogg Video File'), $ogg); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('mp4fID', t('MP4')); ?>
        <?php echo $fileManager->video('ccm-b-mp4-file', 'mp4fID', t('Choose MP4 Video File'), $mp4); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Video Size'); ?>
    </legend>

    <div class="form-group">
        <div class="form-check">
            <label for="videoSizeDefault" class="form-check-label">
                <?php echo $form->radio('videoSize', '0', $videoSize, ['name' => 'videoSize', 'id' => 'videoSizeDefault']); ?>
                <?php echo t('Default Video Dimensions'); ?>
            </label>
        </div>

        <div class="form-check">
            <label for="videoSizeFullWidth" class="form-check-label">
                <?php echo $form->radio('videoSize', '1', $videoSize, ['name' => 'videoSize', 'id' => 'videoSizeFullWidth']); ?>
                <?php echo t('Full Width'); ?>
            </label>
        </div>

        <div class="form-check">
            <label for="videoSizeMaxWidth" class="form-check-label">
                <?php echo $form->radio('videoSize', '2', $videoSize, ['name' => 'videoSize', 'id' => 'videoSizeMaxWidth']); ?>
                <?php echo t('Set Max Width'); ?>
            </label>
        </div>
    </div>

    <div id="video-width" class="form-group" style="display: none;">
        <?php echo $form->label('width', t('Max Width')); ?>

        <div class="input-group">
            <?php echo $form->number('width', $width ?: '', ['min' => 1]); ?>

            <span class="input-group-text">
                <?php echo t('px'); ?>
            </span>
        </div>
    </div>


</fieldset>


<fieldset>
    <legend>
        <?php echo t('Title'); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->text('title', $title ?? ''); ?>
    </div>
</fieldset>


<script>
    $(document).ready(function () {
        if ($('input[name=videoSize]:checked').val() === '2') {
            $('#video-width').show();
        }

        $('input[name=videoSize]').on('change', function () {
            if ($(this).val() === '2') {
                $('#video-width').show();
                $('#width').attr('min', 1);
            } else {
                $('#video-width').hide();
                $('#width').attr('min', 0);
            }
        });
    });
</script>
