<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
if (!$bObj->vWidth) {
	$bObj->vWidth=425;
}
if (!$bObj->vHeight) {
	$bObj->vHeight=344;
}
?>
<style>
    .ccm-ui .form-control.yt-vid-dims {
        width: 100px;
    }
</style>
<div class="form-group">
    <label><?=t('Title')?></label>
    <input type="text" class="form-control" name="title" value="<?=$bObj->title?>"/>
</div>
<div class="form-group">
    <label><?=t('YouTube URL')?></label>
    <input type="text" class="form-control" id="YouTubeVideoURL" name="videoURL" value="<?=$bObj->videoURL?>" />
</div>
<div class="form-group">
    <label><?=t('Width')?></label>
    <input type="text" class="form-control yt-vid-dims" id="YouTubeVideoWidth" name="vWidth" value="<?=$bObj->vWidth?>" />
</div>
<div class="form-group">
    <label><?=t('Height')?></label>
    <input type="text" class="form-control yt-vid-dims" id="YouTubeVideoHeight" name="vHeight" value="<?=$bObj->vHeight?>" />
</div>
<div class="form-group">
    <label><?=t('Video Player')?></label>
        <div class="radio">
            <label><input type="radio" name="vPlayer" value="1" <?=($bObj->vPlayer)?'checked':''?> /> <?=t('iFrame - Works in more devices')?></label>
        </div>
    <div class="radio">
        <label>
            <input type="radio" name="vPlayer" value="0" <?=(!$bObj->vPlayer)?'checked':''?> /> <?=t('Flash Embed - Legacy method')?>
        </label>
    </div>
</div>
