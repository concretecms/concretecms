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
				<?=$form->radio('enablePostingFromAggregator', 0, $enablePostingFromAggregator)?>
				<span><?=t('No, posting is disabled.')?></span>
			</label>
			<label class="radio">
				<?=$form->radio('enablePostingFromAggregator', 1, $enablePostingFromAggregator)?>
				<span><?=t('Yes, this aggregator can be posted to from the front-end.')?></span>
			</label>
		</div>
	</div>
	<div class="control-group" data-row="enablePostingFromAggregator">
		<label class="control-label"><?=t('Create pages using')?></label>
		<div class="controls" data-select="page">
			<?=$form->select('cmpID', $types, $cmpID)?>
		</div>
	</div>
</div>

<script type="text/javascript">
$(function() {
	$('input[name=enablePostingFromAggregator]').on('change', function() {
		var pg = $('input[name=enablePostingFromAggregator]:checked');
		if (pg.val() == 1) {
			$('div[data-row=enablePostingFromAggregator]').show();
		} else {
			$('div[data-row=enablePostingFromAggregator]').hide();
		}
	}).trigger('change');
});
</script>
</div>