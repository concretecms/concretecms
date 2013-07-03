<?
defined('C5_EXECUTE') or die("Access Denied.");

$url       = parse_url($videoURL);
$pathParts = explode('/', rtrim($url['path'], '/'));
$videoID   = end($pathParts);

if (isset($url['query'])) {
	parse_str($url['query'], $query);
	$videoID = (isset($query['v'])) ? $query['v'] : $videoID;
}

$vWidth  = ($vWidth)  ? $vWidth  : 425;
$vHeight = ($vHeight) ? $vHeight : 344;

if (Page::getCurrentPage()->isEditMode()) { ?>

	<div class="ccm-edit-mode-disabled-item" style="width: <?= $vWidth; ?>px; height: <?= $vHeight; ?>px;">
		<div style="padding:8px 0px; padding-top: <?= round($vHeight/2)-10; ?>px;"><?= t('YouTube Video disabled in edit mode.'); ?></div>
	</div>
	
<? } elseif ($vPlayer == 1) { ?>

	<div id="youtube<?= $bID; ?>" class="youtubeBlock">
		<iframe class="youtube-player" width="<?= $vWidth; ?>" height="<?= $vHeight; ?>" src="http://www.youtube.com/embed/<?= $videoID; ?>" frameborder="0" allowfullscreen></iframe>
	</div>
	
<? } else { ?>

	<div id="youtube<?= $bID; ?>" class="youtubeBlock"><div id="youtube<?= $bID; ?>_video"><?= t('You must install Adobe Flash to view this content.'); ?></div></div>
	<script type="text/javascript">
	//<![CDATA[
	params = {
		wmode: "transparent"
	};
	flashvars = {};
	swfobject.embedSWF('http://www.youtube.com/v/<?= $videoID; ?>&amp;hl=en', 'youtube<?= $bID; ?>_video', '<?= $vWidth; ?>', '<?= $vHeight; ?>', '8.0.0', false, flashvars, params);
	//]]>
	</script>
	
<? } ?>
