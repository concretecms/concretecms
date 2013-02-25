<? defined('C5_EXECUTE') or die("Access Denied."); ?>  
<?
if ($controller->getTask() == 'add') {
	$enablePosting = 1;
}
?>
<fieldset>
	<legend><?=t('Posting')?></legend>
	<div class="control-group">
		<label class="control-label"><?=t('Enable Posting')?></label>
		<div class="controls">
			<label class="radio">
				<?=$form->radio('enablePosting', 1, $enablePosting)?>
				<span><?=t('Yes, this conversation accepts messages and replies.')?></span>
			</label>
			<label class="radio">
				<?=$form->radio('enablePosting', 0, $enablePosting)?>
				<span><?=t('No, posting is disabled.')?></span>
			</label>
		</div>
	</div>
</fieldset>
