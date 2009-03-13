<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<div id="ccm-slideshowBlock-fsRow" class="ccm-slideshowBlock-fsRow" >
	<div class="backgroundRow" style="padding-left: 100px">
		<strong>File Set: <span class="ccm-slideshowBlock-fsName"><?=$fsInfo['fsName']?></span></strong><br/><br/>
		<?=t('Duration')?>: <input type="text" name="duration[]" value="<?=intval($fsInfo['duration'])?>" style="vertical-align: middle; width: 30px" />
		&nbsp;
		<?=t('Fade Duration')?>: <input type="text" name="fadeDuration[]" value="<?=intval($fsInfo['fadeDuration'])?>" style="vertical-align: middle; width: 30px" />
		<div style="margin-top:4px">
		<input type="hidden" name="fsID" value="<?=$fsInfo['fsID']?>">
		<input type="hidden" name="fsName" value="<?=$fsInfo['fsName']?>">
		</div>
	</div>
</div>
