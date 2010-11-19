<?php   
defined('C5_EXECUTE') or die("Access Denied.");

$width = 425;
$height = 350;
if (isset($_GET['swfWidth']) || isset($_GET['swfHeight'])) {
	$width = $_GET['swfWidth'];
	$height = $_GET['swfHeight'];
}
?>
<table cellpadding="0" cellspacing="0" style="margin:auto"><tr><td>
<div style="padding:16px 16px 8px 16px; border:1px solid #000; background:#fff"> 
	<?php  if($_GET['local']) { ?>
		<embed align="center" flashvars="myVideoWidth=<?php echo $width?>&myVideoHeight=<?php echo $height?>&myVideoURL=<?php echo DIR_REL?><?php echo $_GET['src']?>" src="<?php echo DIR_REL?>/tools/blocks/video/videoPlayer.swf" type="application/x-shockwave-flash" wmode="opaque" width="<?php echo $width?>" height="<?php echo $height?>" />
	<?php  } else { ?>
		<embed align="center" src="<?php echo $_GET['src']?>" type="application/x-shockwave-flash" wmode="opaque" width="<?php echo $width?>" height="<?php echo $height?>" />
	<?php  } ?>
		
	<div id="popupClose" class="clear" style="margin-top:4px">
	<a href="#" onclick="hideWipeBox(); return false">Close Window</a>
	</div>	
	<div class="spacer"></div>	
</div>
</td></tr></table>