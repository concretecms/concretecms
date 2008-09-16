<?  
$width = 425;
$height = 350;
if (isset($_GET['swfWidth']) || isset($_GET['swfHeight'])) {
	$width = $_GET['swfWidth'];
	$height = $_GET['swfHeight'];
}
?>
<table cellpadding="0" cellspacing="0" style="margin:auto"><tr><td>
<div style="padding:16px 16px 8px 16px; border:1px solid #000; background:#fff"> 
	<? if($_GET['local']) { ?>
		<embed align="center" flashvars="myVideoWidth=<?=$width?>&myVideoHeight=<?=$height?>&myVideoURL=<?=DIR_REL?><?=$_GET['src']?>" src="<?=DIR_REL?>/tools/blocks/video/videoPlayer.swf" type="application/x-shockwave-flash" wmode="opaque" width="<?=$width?>" height="<?=$height?>" />
	<? } else { ?>
		<embed align="center" src="<?=$_GET['src']?>" type="application/x-shockwave-flash" wmode="opaque" width="<?=$width?>" height="<?=$height?>" />
	<? } ?>
		
	<div id="popupClose" class="clear" style="margin-top:4px">
	<a href="#" onclick="hideWipeBox(); return false">Close Window</a>
	</div>	
	<div class="spacer"></div>	
</div>
</td></tr></table>