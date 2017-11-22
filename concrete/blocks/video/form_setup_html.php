<?php defined('C5_EXECUTE') or die('Access Denied.');

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$al = $app->make('helper/concrete/asset_library');

$webm = $controller->getWebmFileID() > 0 ? $controller->getWebMFileObject() : null;
$ogg = $controller->getOggFileID() > 0 ? $controller->getOggFileObject() : null;
$mp4 = $controller->getMp4FileID() > 0 ? $controller->getMp4FileObject() : null;
$poster = $controller->getPosterFileID() > 0 ? $controller->getPosterFileObject() : null;

if (!isset($videoSize)) {
    $videoSize = 0;
}
if (!isset($width)) {
    $width = null;
}
?>

<fieldset>
    <legend><?= t('Video Files'); ?></legend>
    <div class="form-group">
        <label class="control-label"><?= t('Video Placeholder Image (Optional)'); ?></label>
        <?= $al->image('ccm-b-poster-file', 'posterfID', t('Choose Video Placeholder Image'), $poster); ?>
    </div>
    <div class="form-group">
        <label class="control-label"><?= t('WebM'); ?></label>
        <?= $al->video('ccm-b-webm-file', 'webmfID', t('Choose WebM Video File'), $webm); ?>
    </div>
    <div class="form-group">
        <label class="control-label"><?= t('Ogg'); ?></label>
        <?= $al->video('ccm-b-ogg-file', 'oggfID', t('Choose Ogg Video File'), $ogg); ?>
    </div>
    <div class="form-group">
        <label class="control-label"><?= t('MP4'); ?></label>
        <?= $al->video('ccm-b-mp4-file', 'mp4fID', t('Choose MP4 Video File'), $mp4); ?>
    </div>
</fieldset>

<fieldset>
    <legend><?= t('Video Size'); ?></legend>
    <div class="form-group">
        <div class="radio">
            <label>
                <input type="radio" name="videoSize" value="0" <?php echo $videoSize > 0 ? '' : 'checked'; ?>><?php echo t('Default Video Dimensions'); ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="videoSize" value="1" <?php echo $videoSize == 1 ? 'checked' : ''; ?>><?php echo t('Full Width'); ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="videoSize" value="2" <?php echo $videoSize == 2 ? 'checked' : ''; ?>><?php echo t('Set Max Width'); ?>
            </label>
        </div>
    </div>
    <div id="video-width" class="form-group" style="display: none;">
        <?= $form->label('width', t('Max Width')); ?>
        <div class="input-group">
            <?= $form->number('width', $width ?: '', array('min' => 1)); ?>
            <div class="input-group-addon"><?= t('px'); ?></div>
        </div>
    </div>
</fieldset>

<script>
$(document).ready(function() {
    if ($('input[name=videoSize]:checked').val() === '2') {
        $('#video-width').show();
    }

    $('input[name=videoSize]').on('change', function() {
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
