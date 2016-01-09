<? 
defined('C5_EXECUTE') or die("Access Denied.");

$bObj=$controller;
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
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
    <label><?=t('Video Files')?></label>
    <span class="help-text"><?php echo t('Include WebM, Ogg, and MP4 file types for best cross-browser results') ?></span>
    <div class="form-group">
        <label><?php echo t('Video Placeholder Image'); ?></label>
        <?=$al->image('ccm-b-poster-file', 'posterfID', t('Choose Video Placeholder Image (Optional)'));?>
    </div>
    <div class="form-group">
        <label>WebM</label>
        <?=$al->video('ccm-b-webm-file', 'webmfID', t('Choose Video File'));?>
    </div>
    <div class="form-group">
        <label>Ogg</label>
        <?=$al->video('ccm-b-ogg-file', 'oggfID', t('Choose Video File'));?>
    </div>
    <div class="form-group">
        <label>MP4</label>
        <?=$al->video('ccm-b-mp4-file', 'mp4fID', t('Choose Video File'));?>
    </div>
</fieldset>
<? $this->inc('form_setup_html.php'); ?> 
