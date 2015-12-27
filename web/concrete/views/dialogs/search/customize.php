<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">

<form method="post" action="<?=$controller->action('submit')?>" data-dialog-form="search-customize">

	<fieldset>
		<legend><?=t('Choose Columns')?></legend>

	<div class="form-group">
		<label class="control-label"><?=t('Standard Properties')?></label>

	<?php
    $columns = $fldca->getColumns();
    foreach ($columns as $col) { ?>

		<div class="checkbox"><label><?=$form->checkbox($col->getColumnKey(), 1, $fldc->contains($col))?> <span><?=$col->getColumnName()?></span></label></div>

	<?php } ?>

	</div>

	<div class="form-group">
		<label class="control-label"><?=t('Additional Attributes')?></label>

	<?php foreach ($list as $ak) {
		$ak = $ak->getAttributeKey();
		?>

		<div class="checkbox"><label><?=$form->checkbox('ak_' . $ak->getAttributeKeyHandle(), 1, $fldc->contains($ak))?> <span><?=$ak->getAttributeKeyDisplayName()?></span></label></div>

	<?php } ?>

	</div>

	<fieldset>
		<legend><?=t('Column Order')?></legend>

		<p><?=t('Click and drag to change column order.')?></p>

		<ul class="list-unstyled" data-search-column-list="<?=$type?>">
		<?php foreach ($fldc->getColumns() as $col) { ?>
			<li style="cursor: move" data-field-order-column="<?=$col->getColumnKey()?>"><input type="hidden" name="column[]" value="<?=$col->getColumnKey()?>" /><?=$col->getColumnName()?></li>
		<?php } ?>
		</ul>
	</fieldset>

	<fieldset>
		<legend><?=t('Sort By')?></legend>
		<?php $ds = $fldc->getDefaultSortColumn(); ?>

		<div class="form-group">
			<label class="control-label" for="fSearchDefaultSort"><?=t('Default Column')?></label>
			<select <?php if (count($fldc->getSortableColumns()) == 0) { ?>disabled="true"<?php } ?> class="form-control" data-search-select-default-column="<?=$type?>" id="fSearchDefaultSort" name="fSearchDefaultSort">
			<?php foreach ($fldc->getSortableColumns() as $col) { ?>
				<option id="<?=$col->getColumnKey()?>" value="<?=$col->getColumnKey()?>" <?php if ($col->getColumnKey() == $ds->getColumnKey()) { ?> selected="true" <?php } ?>><?=$col->getColumnName()?></option>
			<?php } ?>
			</select>
		</div>

		<div class="form-group">
			<label class="control-label" for="fSearchDefaultSortDirection"><?=t('Direction')?></label>
			<select <?php if (count($fldc->getSortableColumns()) == 0) { ?>disabled="true"<?php } ?> class="form-control" id="fSearchDefaultSortDirection" name="fSearchDefaultSortDirection">
				<option value="asc" <?php if ($ds->getColumnDefaultSortDirection() == 'asc') { ?> selected="true" <?php } ?>><?=t('Ascending')?></option>
				<option value="desc" <?php if ($ds->getColumnDefaultSortDirection() == 'desc') { ?> selected="true" <?php } ?>><?=t('Descending')?></option>
			</select>
		</div>

	</fieldset>

	<div class="dialog-buttons">
	<button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
	<button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
	</div>

</form>
</div>
