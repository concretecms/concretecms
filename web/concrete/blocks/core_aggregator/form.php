<? defined('C5_EXECUTE') or die("Access Denied."); ?>  

<?=Loader::helper('concrete/interface')->tabs(array(
	array('sources', t('Data Sources'), true),
	array('output', t('Output'))
));?>


<div class="ccm-tab-content" id="ccm-tab-content-sources">

<?
if ($controller->getTask() == 'add') { 
	$itemsPerPage = 20;
}

if ($controller->getTask() == 'edit') { ?>
<div class="alert alert-warning ccm-aggregator-data-source-update-detected">
	<?=t("Data source change detected. If saved, this block will clear its current tiles and grab new ones.")?>
	<input type="hidden" name="rescanAggregatorItems" value="0" />
</div>
<? } ?>

<div class="form-inline">
<div class="control-group">
	<label class="control-label"><?=t('Add Source')?></label>
	<div class="controls">
		<select name="addSource" class="span2">
			<? foreach($availableSources as $ags) { ?>
			<option value="<?=$ags->getAggregatorDataSourceID()?>"><?=$ags->getAggregatorDataSourceName()?></option>
			<? } ?>
		</select>
		<button class="btn" type="button" data-submit="add-source"><?=t('Add')?></button>
	</div>
</div>
</div>

<div id="ccm-aggregator-data-source-templates" style="display: none">

<? foreach($availableSources as $ags) { ?>

<fieldset data-aggregator-data-source-options-form="<?=$ags->getAggregatorDataSourceID()?>">
	<div style="margin-bottom: 8px">
		<input type="hidden" name="agsID[_ags_]" value="<?=$ags->getAggregatorDataSourceID()?>" />
	<a href="#" style="float: right" data-delete="aggregator-source"><i class="icon-minus-sign"></i></a>

		<? $source = $ags; ?>
		<? include($ags->getAggregatorDataSourceOptionsForm())?>
		<hr/>
	</div>
</fieldset>

<? } ?>

</div>

<div id="ccm-aggregator-data-source-templates-selected">

<? if (count($activeSources) > 0) { ?>
<? foreach($activeSources as $key => $configuration) { ?>

	<fieldset data-aggregator-data-source-selected="<?=$configuration->getAggregatorDataSourceID()?>">
		<div style="margin-bottom: 8px">
			<input type="hidden" name="agsID[<?=$key?>]" value="<?=$configuration->getAggregatorDataSourceID()?>" />
		<a href="#" style="float: right" data-delete="aggregator-source"><i class="icon-minus-sign"></i></a>

			<?
			$source = $configuration; 
			$source->setOptionFormKey($key);
			include($configuration->getAggregatorDataSourceOptionsForm());
			?>
			<hr/>
		</div>
	</fieldset>

<? } ?>

<? } else { ?>
	<span data-message="no-sources"><?=t('You have not added any data sources.')?></span></div>
<? } ?>
</div>
</div>

<div class="ccm-tab-content form-horizontal" id="ccm-tab-content-output">
		<div class="control-group" data-row="itemsPerPage">
		<label class="control-label"><?=t('Items Per Page')?></label>
		<div class="controls">
			<?=$form->text('itemsPerPage', $itemsPerPage, array('class' => 'span1'))?>
		</div>
	</div>

</div>



<script type="text/javascript">
$(function() {
	$('button[data-submit=add-source]').on('click', function() {
		var agsID = $('select[name=addSource]').val();
		$("span[data-message=no-sources]").remove();
		var $fds = $('fieldset[data-aggregator-data-source-options-form=' + agsID + ']').clone();
		$fds.removeAttr('data-aggregator-data-source-options-form').attr('data-aggregator-data-source-selected', agsID).appendTo('#ccm-aggregator-data-source-templates-selected');
		var totalsources = $('#ccm-aggregator-data-source-templates-selected fieldset[data-aggregator-data-source-selected]').length;
		var key = totalsources - 1;
		var html = $fds.html();
		$('#ccm-aggregator-data-source-templates-selected').trigger('change');
		html = html.replace(/\[_ags_\]/gi, '[' + key + ']');
		$fds.html(html);
	});
	$('#ccm-aggregator-data-source-templates-selected').on('click', 'a[data-delete=aggregator-source]', function() {
		$(this).closest('fieldset[data-aggregator-data-source-selected]').remove();
		var totalsources = $('#ccm-aggregator-data-source-templates-selected fieldset[data-aggregator-data-source-selected]').length;
		$('#ccm-aggregator-data-source-templates-selected').trigger('change');
		if (!totalsources) {
			$('#ccm-aggregator-data-source-templates-selected').html("<span data-message=\"no-sources\"><?=t('You have not added any data sources.')?></span>");
		}
		return false;
	});

	<? if ($controller->getTask() == 'edit') { ?>
		$('a[data-tab=output]').trigger('click');
		$('#ccm-aggregator-data-source-templates-selected').on('change', function() {
			$('div.ccm-aggregator-data-source-update-detected').removeClass('ccm-aggregator-data-source-update-detected');
			$('input[name=rescanAggregatorItems]').val('1');
		});
	<? } ?>
});
</script>

<style type="text/css">
#ccm-aggregator-data-source-templates-selected {
	margin-top: 18px;
}
.ccm-aggregator-data-source-update-detected {
	display: none;
}

</style>