<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$bObj=$controller;
	$al = Loader::helper('concrete/asset_library');
	$bf = null;
    if ($controller->getWebmFileID() > 0) {
        $webm = $controller->getWebMFileObject();
    }
    if ($controller->getOggFileID() > 0) {
        $ogg = $controller->getOggFileObject();
    }
    if ($controller->getMp4FileID() > 0) {
        $mp4 = $controller->getMp4FileObject();
    }

    if ($controller->getPosterFileID() > 0) {
        $poster = $controller->getPosterFileObject();
    }
?>
<style>
    .help-text {
        color: #ddd;
        display: block;
        line-height: 1.3em;
        font-size: 11px;
        margin-bottom: 20px;
    }
</style>
<fieldset>
    <div class="form-group">
    <label><?=t('Video Files')?></label>
        <span class="help-text"><?php echo t('Include WebM, Ogg, and MP4 file types for best cross-browser results') ?></span>
        <div class="form-group">
            <label><?php echo t('Video Placeholder Image'); ?></label>
            <?=$al->image('ccm-b-poster-file', 'posterfID', t('Choose Video Placeholder Image (Optional)'), $poster);?>
        </div>
        <div class="form-group">
            <label>WebM</label>
            <?=$al->video('ccm-b-webm-file', 'webmfID', t('Choose WebM Video File'), $webm);?>
        </div>
        <div class="form-group">
            <label>Ogg</label>
            <?=$al->video('ccm-b-ogg-file', 'oggfID', t('Choose Video File'), $ogg);?>
        </div>
        <div class="form-group">
            <label>MP4</label>
            <?=$al->video('ccm-b-mp4-file', 'mp4fID', t('Choose Video File'), $mp4);?>
        </div>
</fieldset>
<? $this->inc('form_setup_html.php'); ?> 