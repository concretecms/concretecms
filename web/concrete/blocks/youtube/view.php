<?
defined('C5_EXECUTE') or die("Access Denied.");
$url = parse_url($videoURL);
parse_str($url['query'], $query);
parse_str($url['path'], $path);
$c = Page::getCurrentPage();

if (!$vWidth) {
	$vWidth=425;
}
if (!$vHeight) {
	$vHeight=344;
}

if ($c->isEditMode()) { ?>
	<div class="ccm-edit-mode-disabled-item" style="width:<?php echo $vWidth; ?>px; height:<?php echo $vHeight; ?>px;">
		<div style="padding:8px 0px; padding-top: <?php echo round($vHeight/2)-10; ?>px;"><?php echo t('YouTube Video disabled in edit mode.'); ?></div>
	</div>
<? } elseif ($vPlayer==1) { ?>
	
	<div id="youtube<?php echo $bID?>" class="youtubeBlock">
	
	<?php if($url['host'] == 'youtu.be') { ?>
		<iframe class="youtube-player" type="text/html" width="<?php  echo $vWidth; ?>" height="<?php  echo $vHeight; ?>" src="http://www.youtube.com/embed/<?php echo $url['path']?>/<?php echo (strpos($url['path'], '@')) ? '@' : '?'; ?>wmode=transparent" frameborder="0"></iframe>
	<?php }else { ?>
		<iframe class="youtube-player" type="text/html" width="<?php  echo $vWidth; ?>" height="<?php  echo $vHeight; ?>" src="http://www.youtube.com/embed/<?php echo $query['v']?>/<?php echo (strpos($query['v'], '@')) ? '@' : '?'; ?>wmode=transparent" frameborder="0"></iframe>
	<?php } ?>
	</div>
<? } else { ?>
	
	<div id="youtube<?php echo $bID?>" class="youtubeBlock"><div id="youtube<?php echo $bID?>_video"><?php echo t('You must install Adobe Flash to view this content.')?></div></div>
	
	<?php 
	
	if($url['host'] == 'youtu.be') { ?>
		<script type="text/javascript">
		//<![CDATA[
		params = {
			wmode:  "transparent"
		};
		flashvars = {};
		swfobject.embedSWF('http://www.youtube.com/v<?=$url['path']?>&amp;hl=en', 'youtube<?php echo $bID?>_video', '<?php echo $vWidth; ?>', '<?php echo $vHeight; ?>', '8.0.0', false, flashvars, params);
		//]]>
		</script>
	<? }else{ ?>
		<script type="text/javascript">
		//<![CDATA[
		params = {
			wmode:  "transparent"
		};
		flashvars = {};
		swfobject.embedSWF('http://www.youtube.com/v/<?=$query['v']?>&amp;hl=en', 'youtube<?php echo $bID?>_video', '<?php echo $vWidth; ?>', '<?php echo $vHeight; ?>', '8.0.0', false, flashvars, params);
		//]]>
		</script>
	<? } ?>
<? } ?>