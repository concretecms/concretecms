<? defined('C5_EXECUTE') or die("Access Denied."); ?>  
<?
if ($controller->getTask() == 'add') {
	$enableNewTopics = 1;
}

$composers = Composer::getList();
$types = array();
foreach($composers as $cmp) {
	$types[$cmp->getComposerID()] = $cmp->getComposerName();
}

?>
<div class="form-horizontal">

<fieldset>
	<legend><?=t('Posting')?></legend>
	<div class="control-group">
		<label class="control-label"><?=t('Enable New Topics')?></label>
		<div class="controls">
			<label class="radio">
				<?=$form->radio('enableNewTopics', 1, $enableNewConversations)?>
				<span><?=t('Yes, this conversation is open and new topics can be posted.')?></span>
			</label>
			<label class="radio">
				<?=$form->radio('enableNewTopics', 0, $enableNewConversations)?>
				<span><?=t('No, posting is disabled.')?></span>
			</label>
		</div>
	</div>
	<div class="control-group" data-row="enableNewConversations">
		<label class="control-label"><?=t('Create Conversation using Composer')?></label>
		<div class="controls" data-select="page">
			<?=$form->select('cmpID', $types, $cmpID)?>
		</div>
	</div>
</fieldset>
</div>

<script type="text/javascript">
$(function() {
	$('input[name=enableNewTopics]').on('change', function() {
		var pg = $('input[name=enableNewTopics]:checked');
		if (pg.val() == 1) {
			$('div[data-row=enableNewTopics]').show();
		} else {
			$('div[data-row=enableNewTopics]').hide();
		}
	}).trigger('change');
});
</script>
