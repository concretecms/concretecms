<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">

<form method="post" action="<?=$controller->action('submit')?>" data-dialog-form="search-customize">

	<fieldset>
		<legend><?=t('Choose Columns')?></legend>
	
	<div class="form-group">
		<label class="control-label"><?=t('Standard Properties')?></label>
		
	<?
	$columns = $fldca->getColumns();
	foreach($columns as $col) { ?>

		<div class="checkbox"><label><?=$form->checkbox($col->getColumnKey(), 1, $fldc->contains($col))?> <span><?=$col->getColumnName()?></span></label></div>
	
	<? } ?>
	
	</div>
	
	
	<div class="form-group">
		<label class="control-label"><?=t('Additional Attributes')?></label>
		
	
	<? foreach($list as $ak) { ?>

		<div class="checkbox"><label><?=$form->checkbox('ak_' . $ak->getAttributeKeyHandle(), 1, $fldc->contains($ak))?> <span><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></span></label></div>
	
	<? } ?>
	
	</div>

	<fieldset>
		<legend><?=t('Column Order')?></legend>
		
		<p><?=t('Click and drag to change column order.')?></p>
		
		<ul class="list-unstyled" data-search-column-list="<?=$type?>">
		<? foreach($fldc->getColumns() as $col) { ?>
			<li data-field-order-column="<?=$col->getColumnKey()?>"><input type="hidden" name="column[]" value="<?=$col->getColumnKey()?>" /><?=$col->getColumnName()?></li>	
		<? } ?>	
		</ul>		
	</fieldset>

	<fieldset>
		<legend><?=t('Sort By')?></legend>
		<? $ds = $fldc->getDefaultSortColumn(); ?>
	
		<div class="form-group">
			<label class="control-label" for="fSearchDefaultSort"><?=t('Default Column')?></label>
			<select <? if (count($fldc->getSortableColumns()) == 0) { ?>disabled="true"<? } ?> class="form-control" data-search-select-default-column="<?=$type?>" id="fSearchDefaultSort" name="fSearchDefaultSort">
			<? foreach($fldc->getSortableColumns() as $col) { ?>
				<option id="<?=$col->getColumnKey()?>" value="<?=$col->getColumnKey()?>" <? if ($col->getColumnKey() == $ds->getColumnKey()) { ?> selected="true" <? } ?>><?=$col->getColumnName()?></option>
			<? } ?>	
			</select>
		</div>

		<div class="form-group">
			<label class="control-label" for="fSearchDefaultSortDirection"><?=t('Direction')?></label>
			<select <? if (count($fldc->getSortableColumns()) == 0) { ?>disabled="true"<? } ?> class="form-control" id="fSearchDefaultSortDirection" name="fSearchDefaultSortDirection">
				<option value="asc" <? if ($ds->getColumnDefaultSortDirection() == 'asc') { ?> selected="true" <? } ?>><?=t('Ascending')?></option>
				<option value="desc" <? if ($ds->getColumnDefaultSortDirection() == 'desc') { ?> selected="true" <? } ?>><?=t('Descending')?></option>	
			</select>	
		</div>

	</fieldset>

	<div class="dialog-buttons">
	<button class="btn pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
	<button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
	</div>

</form>
</div>