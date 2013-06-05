<?php
defined('C5_EXECUTE') or die("Access Denied.");

$composers = Composer::getList();
$types = array();
foreach($composers as $cmp) {
	$types[$cmp->getComposerID()] = $cmp->getComposerName();
}

?>

<input type="hidden" name="tab[]" value="posting" />

<div class="form-horizontal">
	<div class="control-group">
		<label class="control-label"><?=t('Enable New Topics')?></label>
		<div class="controls">
			<label class="radio">
				<?=$form->radio('enablePostingFromGathering', 0, $enablePostingFromGathering)?>
				<span><?=t('No, posting is disabled.')?></span>
			</label>
			<label class="radio">
				<?=$form->radio('enablePostingFromGathering', 1, $enablePostingFromGathering)?>
				<span><?=t('Yes, this gathering can be posted to from the front-end.')?></span>
			</label>
		</div>
	</div>
	<div class="control-group" data-row="enablePostingFromGathering">
		<label class="control-label"><?=t('Create pages using')?></label>
		<div class="controls" data-select="page">
			<?=$form->select('cmpID', $types, $cmpID)?>
		</div>
	</div>
</div>

<script type="text/javascript">
$(function() {
	$('input[name=enablePostingFromGathering]').on('change', function() {
		var pg = $('input[name=enablePostingFromGathering]:checked');
		if (pg.val() == 1) {
			$('div[data-row=enablePostingFromGathering]').show();
		} else {
			$('div[data-row=enablePostingFromGathering]').hide();
		}
	}).trigger('change');
});
</script>
</div>
