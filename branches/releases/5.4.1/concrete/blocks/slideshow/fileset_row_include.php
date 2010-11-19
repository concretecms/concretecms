<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<div id="ccm-slideshowBlock-fsRow" class="ccm-slideshowBlock-fsRow" >
	<div class="backgroundRow" style="padding-left: 100px">
		<strong>File Set:</strong> <span class="ccm-file-set-pick-cb"><?php echo $form->select('fsID', $fsInfo['fileSets'], $fsInfo['fsID'])?></span><br/><br/>
		<?php echo t('Duration')?>: <input type="text" name="duration[]" value="<?php echo intval($fsInfo['duration'])?>" style="vertical-align: middle; width: 30px" />
		&nbsp;
		<?php echo t('Fade Duration')?>: <input type="text" name="fadeDuration[]" value="<?php echo intval($fsInfo['fadeDuration'])?>" style="vertical-align: middle; width: 30px" />
	</div>
</div>
