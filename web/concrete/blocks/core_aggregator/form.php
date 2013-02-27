<? defined('C5_EXECUTE') or die("Access Denied."); ?>  
<? foreach($availableSources as $ags) { ?>
	
<fieldset>
	<label class="checkbox">
		<input type="checkbox" <? if (array_key_exists($ags->getAggregatorDataSourceID(), $activeSources)) { ?>checked="checked"<? } ?> value="<?=$ags->getAggregatorDataSourceID()?>" data-aggregator-data-source-checkbox="<?=$ags->getAggregatorDataSourceID()?>" name="source[]" /> <span><?=$ags->getAggregatorDataSourceName()?></span>
	</label>

	<div style="display: none" data-aggregator-data-source-options-form="<?=$ags->getAggregatorDataSourceID()?>">
		<? $configuration = $activeSources[$ags->getAggregatorDataSourceID()]; ?>
		<? include($ags->getAggregatorDataSourceOptionsForm())?>
	</div>

</fieldset>

<script type="text/javascript">
$(function() {

	$('input[data-aggregator-data-source-checkbox]').on('change', function() {
		if ($(this).is(':checked')) {
			$('div[data-aggregator-data-source-options-form=' + $(this).attr('data-aggregator-data-source-checkbox') + ']').show();
		} else {
			$('div[data-aggregator-data-source-options-form=' + $(this).attr('data-aggregator-data-source-checkbox') + ']').hide();
		}		
	}).trigger('change');

});
</script>
<? } ?>