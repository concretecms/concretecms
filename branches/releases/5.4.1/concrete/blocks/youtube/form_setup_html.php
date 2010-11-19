<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<style type="text/css">
table#videoBlockSetup th {font-weight: bold; text-style: normal; padding-right: 8px; white-space: nowrap; vertical-align:top ; padding-bottom:8px}
table#videoBlockSetup td{ font-size:12px; vertical-align:top; padding-bottom:8px;}
table#videoBlockSetup .note{ font-size:10px; color:#999999; font-weight:normal }
</style> 

<table id="videoBlockSetup" style="width:100%"> 
	<tr>
		<th><?php echo t('Title')?></th>
		<td><input type="text" style="width: 230px" name="title" value="<?php echo $bObj->title?>"/></td>
	</tr>	
	<tr>
		<th><?php echo t('URL')?></th>
		<td>
			<input type="text" style="width: 230px" id="YouTubeVideoURL" name="videoURL" value="<?php echo $bObj->videoURL?>" />
		</td>
	</tr>	
</table>