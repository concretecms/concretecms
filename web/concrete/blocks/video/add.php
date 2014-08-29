<? 
defined('C5_EXECUTE') or die("Access Denied.");

$bObj=$controller;
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>
<div class="form-group">
    <label><?=t('Video Files')?></label>
    <span style="color: #ddd"><?php echo t('Include WebM, Ogg, and MP4 file types for best cross-browser results') ?></span>
    <div class="input">
        <label><?php echo t('Video Placeholder Image'); ?></label>
        <?=$al->image('ccm-b-poster-file', 'posterfID', t('Choose Video Placeholder Image (Optional)'));?>
    </div>
    <div class="input">
        <label>WebM</label>
        <?=$al->video('ccm-b-webm-file', 'webmfID', t('Choose Video File'));?>
    </div>
    <div class="input">
        <label>Ogg</label>
        <?=$al->video('ccm-b-ogg-file', 'oggfID', t('Choose Video File'));?>
    </div>
    <div class="input">
        <label>MP4</label>
        <?=$al->video('ccm-b-mp4-file', 'mp4fID', t('Choose Video File'));?>
    </div>
</div>

<? $this->inc('form_setup_html.php'); ?> 
