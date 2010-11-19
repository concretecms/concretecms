<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<div id="ccm-slideshowBlock-imgRow<?php echo $imgInfo['slideshowImgId']?>" class="ccm-slideshowBlock-imgRow" >
	<div class="backgroundRow" style="background: url(<?php echo $imgInfo['thumbPath']?>) no-repeat left top; padding-left: 100px">
		<div class="cm-slideshowBlock-imgRowIcons" >
			<div style="float:right">
				<a onclick="SlideshowBlock.moveUp('<?php echo $imgInfo['slideshowImgId']?>')" class="moveUpLink"></a>
				<a onclick="SlideshowBlock.moveDown('<?php echo $imgInfo['slideshowImgId']?>')" class="moveDownLink"></a>									  
			</div>
			<div style="margin-top:4px"><a onclick="SlideshowBlock.removeImage('<?php echo $imgInfo['slideshowImgId']?>')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/delete_small.png" /></a></div>
		</div>
		<strong><?php echo $imgInfo['fileName']?></strong><br/><br/>
		<?php echo t('Duration')?>: <input type="text" name="duration[]" value="<?php echo intval($imgInfo['duration'])?>" style="vertical-align: middle; width: 30px" />
		&nbsp;
		<?php echo t('Fade Duration')?>: <input type="text" name="fadeDuration[]" value="<?php echo intval($imgInfo['fadeDuration'])?>" style="vertical-align: middle; width: 30px" />
		&nbsp;
		<?php echo t('Set Number')?>: <input type="text" name="groupSet[]" value="<?php echo intval($imgInfo['groupSet'])?>" style="vertical-align: middle; width: 30px" /><br/>
		<div style="margin-top:4px">
		<?php echo t('Link URL (optional)')?>: <input type="text" name="url[]" value="<?php echo $imgInfo['url']?>" style="vertical-align: middle; font-size: 10px; width: 140px" />
		<input type="hidden" name="imgFIDs[]" value="<?php echo $imgInfo['fID']?>">
		<input type="hidden" name="imgHeight[]" value="<?php echo $imgInfo['imgHeight']?>">
		</div>
	</div>
</div>
