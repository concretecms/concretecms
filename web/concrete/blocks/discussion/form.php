<? defined('C5_EXECUTE') or die("Access Denied."); ?>  
<?
if ($controller->getTask() == 'add') {
	$enableNewConversations = 1;
}

$ctArray = CollectionType::getList();
$types = array('-1' => t('** None Selected'));
foreach($ctArray as $ct) {
	$types[$ct->getCollectionTypeID()] = $ct->getCollectionTypeName();
}

?>
<div class="form-horizontal">

<fieldset>
	<legend><?=t('Posting')?></legend>
	<div class="control-group">
		<label class="control-label"><?=t('Enable New Conversations')?></label>
		<div class="controls">
			<label class="radio">
				<?=$form->radio('enableNewConversations', 1, $enableNewConversations)?>
				<span><?=t('Yes, this conversation is open and new topics can be posted.')?></span>
			</label>
			<label class="radio">
				<?=$form->radio('enableNewConversations', 0, $enableNewConversations)?>
				<span><?=t('No, posting is disabled.')?></span>
			</label>
		</div>
	</div>
	<div class="control-group" data-row="enableNewConversations">
		<label class="control-label"><?=t('Create Conversation with Page Type')?></label>
		<div class="controls" data-select="page">
			<?=$form->select('ctID', $types, $ctID)?>
		</div>
	</div>
</fieldset>
</div>

<script type="text/javascript">
$(function() {
	$('input[name=enableNewConversations]').on('change', function() {
		var pg = $('input[name=enableNewConversations]:checked');
		if (pg.val() == 1) {
			$('div[data-row=enableNewConversations]').show();
		} else {
			$('div[data-row=enableNewConversations]').hide();
		}
	}).trigger('change');
});
</script>
