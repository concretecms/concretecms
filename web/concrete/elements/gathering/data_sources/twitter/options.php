<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
if (is_object($configuration)) { 
	$twitterUsername = $configuration->getTwitterUsername();
}
?>
<div class="control-group">
	<label class="control-label"><?=t('Twitter User')?></label>
	<div class="controls">
		<?=$form->text($source->optionFormKey('twitterUsername'), $twitterUsername)?>
	</div>
</div>
