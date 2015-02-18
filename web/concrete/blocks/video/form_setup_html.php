<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="form-group">
<?=$form->label('ccm-block-video-width', t('Width'))?>
<div class="input">
	<input type="text" style="width: 40px" id="ccm-block-video-width" name="width" value="<?=$width?>"/>
</div>
</div>

<div class="form-group">
<?=$form->label('ccm-block-video-height', t('Height'))?>
<div class="input">
		<input type="text" style="width: 40px" id="ccm-block-video-height" name="height" value="<?=$height?>" />
</div>
</div>
