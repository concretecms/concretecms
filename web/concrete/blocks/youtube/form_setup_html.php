<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<style type="text/css">
table#videoBlockSetup th {font-weight: bold; text-style: normal; padding-right: 8px; white-space: nowrap; vertical-align:top ; padding-bottom:8px}
table#videoBlockSetup td{ font-size:12px; vertical-align:top; padding-bottom:8px;}
table#videoBlockSetup .note{ font-size:10px; color:#999999; font-weight:normal }
</style> 

<?
if (!$bObj->vWidth) {
	$bObj->vWidth=425;
}
if (!$bObj->vHeight) {
	$bObj->vHeight=344;
}

?>

<table id="videoBlockSetup" style="width:100%" class="table table-bordered"> 
	<tr>
		<th><?=t('Title')?></th>
		<td><input type="text" style="width: 230px" name="title" value="<?=$bObj->title?>"/></td>
	</tr>	
	<tr>
		<th><?=t('YouTube URL')?></th>
		<td>
			<input type="text" style="width: 230px" id="YouTubeVideoURL" name="videoURL" value="<?=$bObj->videoURL?>" />
		</td>
	</tr>
	<tr>
		<th><?=t('Width')?></th>
		<td>
			<input type="text" style="width: 40px" id="YouTubeVideoWidth" name="vWidth" value="<?=$bObj->vWidth?>" />
		</td>
	</tr>
	<tr>
		<th><?=t('Height')?></th>
		<td>
			<input type="text" style="width: 40px" id="YouTubeVideoHeight" name="vHeight" value="<?=$bObj->vHeight?>" />
		</td>
	</tr>
	<tr>
		<th><?=t('Video Player')?></th>
		<td>
			<input type="radio" name="vPlayer" value="1" <?=($bObj->vPlayer)?'checked':''?> /> <?=t('iFrame - Works in more devices')?> <br/>
			<input type="radio" name="vPlayer" value="0" <?=(!$bObj->vPlayer)?'checked':''?> /> <?=t('Flash Embed - Legacy method')?>
		</td>
	</tr>	
</table>