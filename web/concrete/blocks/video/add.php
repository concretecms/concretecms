<?php
defined('C5_EXECUTE') or die("Access Denied.");

$bObj = $controller;
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>

<fieldset>
    <legend><?=t('Video Files')?></legend>
    <div class="form-group">
        <label class="control-label"><?php echo t('Video Placeholder Image'); ?></label>
        <?=$al->image('ccm-b-poster-file', 'posterfID', t('Choose Video Placeholder Image (Optional)'));?>
    </div>
    <div class="form-group">
        <label class="control-label"><?=t('WebM')?></label>
        <?=$al->video('ccm-b-webm-file', 'webmfID', t('Choose Video File'));?>
    </div>
    <div class="form-group">
        <label class="control-label"><?=t('OGG')?></label>
        <?=$al->video('ccm-b-ogg-file', 'oggfID', t('Choose Video File'));?>
    </div>
    <div class="form-group">
        <label class="control-label"><?=t('MP4')?></label>
        <?=$al->video('ccm-b-mp4-file', 'mp4fID', t('Choose Video File'));?>
    </div>
</fieldset>
<?php $this->inc('form_setup_html.php'); ?> 
