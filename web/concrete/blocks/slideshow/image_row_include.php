<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<div id="ccm-slideshowBlock-imgRow<?=$imgInfo['slideshowImgId']?>" class="ccm-slideshowBlock-imgRow" >
	<div class="backgroundRow" style="background: url(<?=$imgInfo['thumbPath']?>) no-repeat left top; padding-left: 100px">
		<div class="cm-slideshowBlock-imgRowIcons" >
			<div style="float:right">
				<a onclick="SlideshowBlock.moveUp('<?=$imgInfo['slideshowImgId']?>')" class="moveUpLink"></a>
				<a onclick="SlideshowBlock.moveDown('<?=$imgInfo['slideshowImgId']?>')" class="moveDownLink"></a>									  
			</div>
			<div style="margin-top:4px"><a onclick="SlideshowBlock.removeImage('<?=$imgInfo['slideshowImgId']?>')"><img src="<?=ASSETS_URL_IMAGES?>/icons/delete_small.png" /></a></div>
		</div>
		<strong><?=$imgInfo['fileName']?></strong><br/><br/>
		<?=t('Duration')?>: <input type="text" name="duration[]" value="<?=intval($imgInfo['duration'])?>" style="vertical-align: middle; width: 30px" />
		&nbsp;
		<?=t('Fade Duration')?>: <input type="text" name="fadeDuration[]" value="<?=intval($imgInfo['fadeDuration'])?>" style="vertical-align: middle; width: 30px" />
		&nbsp;
		<?=t('Set Number')?>: <input type="text" name="groupSet[]" value="<?=intval($imgInfo['groupSet'])?>" style="vertical-align: middle; width: 30px" /><br/>
		<div style="margin-top:4px">
		<?=t('Link URL (optional)')?>: <input type="text" name="url[]" value="<?=$imgInfo['url']?>" style="vertical-align: middle; font-size: 10px; width: 140px" />
		<input type="hidden" name="imgFIDs[]" value="<?=$imgInfo['fID']?>">
		<input type="hidden" name="imgHeight[]" value="<?=$imgInfo['imgHeight']?>">
		</div>
	</div>
</div>
