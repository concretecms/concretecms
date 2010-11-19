<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$url = parse_url($videoURL);
parse_str($url['query'], $query);
$c = Page::getCurrentPage();
 
$vWidth=425;
$vHeight=344;
if ($c->isEditMode()) { ?>
	<div class="ccm-edit-mode-disabled-item" style="width:<?php  echo $vWidth; ?>px; height:<?php  echo $vHeight; ?>px;">
		<div style="padding:8px 0px; padding-top: <?php  echo round($vHeight/2)-10; ?>px;"><?php  echo t('Content disabled in edit mode.'); ?></div>
	</div>
	
<?php  } else { ?>

	<div id="youtube<?php  echo $bID?>"><?php  echo t('You must install Adobe Flash to view this content.')?></div>
	<script type="text/javascript">
	//<![CDATA[
	params = {
		wmode:  "transparent",
	};
	flashvars = {};
	swfobject.embedSWF('http://www.youtube.com/v/<?php echo $query['v']?>&amp;hl=en', 'youtube<?php  echo $bID?>', '<?php  echo $vWidth; ?>', '<?php  echo $vHeight; ?>', '8.0.0', false, flashvars, params);
	//]]>
	</script>
<?php  } ?>