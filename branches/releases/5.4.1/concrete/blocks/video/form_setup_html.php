<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<style type="text/css">
table#videoBlockSetup {margin-top:16px}
table#videoBlockSetup th {font-weight: bold; text-style: normal; padding-right: 8px; white-space: nowrap; vertical-align:top ; padding-bottom:8px}
table#videoBlockSetup td{ font-size:12px; vertical-align:top; padding-bottom:8px;}
table#videoBlockSetup .note{ font-size:10px; color:#999999; font-weight:normal }
</style> 

<table id="videoBlockSetup"> 
	<tr>
		<th><?php echo t("Width")?></th>
		<td><input type="text" style="width: 40px" id="ccm-block-video-width" name="width" value="<?php echo $width?>"/></td>
	</tr>	
	<tr>
		<th><?php echo t("Height")?></th>
		<td>
			<input type="text" style="width: 40px" id="ccm-block-video-height" name="height" value="<?php echo $height?>" />
		</td>
	</tr>	
</table>