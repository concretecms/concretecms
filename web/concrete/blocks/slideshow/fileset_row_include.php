<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<div id="ccm-slideshowBlock-fsRow" class="ccm-slideshowBlock-fsRow" >
	<div class="backgroundRow" style="padding-left: 100px">
		<strong><?=t('File Set:')?></strong> <span class="ccm-file-set-pick-cb"><?=$form->select('fsID', $fsInfo['fileSets'], $fsInfo['fsID'])?></span><br/><br/>
		<?=t('Duration')?>: <input type="text" name="duration[]" value="<?=intval($fsInfo['duration'])?>" style="vertical-align: middle; width: 30px" />
		&nbsp;
		<?=t('Fade Duration')?>: <input type="text" name="fadeDuration[]" value="<?=intval($fsInfo['fadeDuration'])?>" style="vertical-align: middle; width: 30px" />
	</div>
</div>
