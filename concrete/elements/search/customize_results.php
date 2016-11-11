<?php
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $provider \Concrete\Core\User\Search\SearchProvider
 */
$available = $provider->getAvailableColumnSet();
$current = $provider->getCurrentColumnSet();
$all = $provider->getAllColumnSet();
$list = $provider->getCustomAttributeKeys();
$form = Core::make('helper/form');

?>

<section data-section="customize-results">

<fieldset>
	<legend><?=t('Choose Columns')?></legend>

	<?php if (count($available->getColumns())) { ?>
	<div class="form-group">
		<?php if (count($list)) { ?>
			<label class="control-label"><?=t('Standard Properties')?></label>
		<?php } ?>
		<?php
		$columns = $available->getColumns();
		foreach ($columns as $col) {
			?>

			<div class="checkbox"><label><?=$form->checkbox($col->getColumnKey(), 1, $current->contains($col))?> <span><?=$col->getColumnName()?></span></label></div>

			<?php
		} ?>

	</div>
	<?php } ?>

	<?php if (count($list)) { ?>
	<div class="form-group">
		<?php if (count($available->getColumns())) { ?>
			<label class="control-label"><?=t('Custom Attributes')?></label>
		<?php } ?>

		<?php foreach ($list as $ak) {
			?>

			<div class="checkbox"><label><?=$form->checkbox('ak_' . $ak->getAttributeKeyHandle(), 1, $current->contains($ak))?> <span><?=$ak->getAttributeKeyDisplayName()?></span></label></div>

			<?php
		} ?>

	</div>

	<?php } ?>

	<fieldset>
		<legend><?=t('Column Order')?></legend>

		<p><?=t('Click and drag to change column order.')?></p>

		<ul class="item-select-list" data-search-column-list="<?=$type?>">
			<?php foreach ($current->getColumns() as $col) {
				?>
				<li style="cursor: move" data-field-order-column="<?=$col->getColumnKey()?>"><input type="hidden" name="column[]" value="<?=$col->getColumnKey()?>" /><?=$col->getColumnName()?>

					<i class="ccm-item-select-list-sort ui-sortable-handle"></i>
				</li>
				<?php
			} ?>
		</ul>
	</fieldset>

	<fieldset>
		<legend><?=t('Sort By')?></legend>
		<?php $ds = $current->getDefaultSortColumn(); ?>

		<div class="form-group">
			<label class="control-label" for="fSearchDefaultSort"><?=t('Default Column')?></label>
			<select <?php if (count($all->getSortableColumns()) == 0) {
					?>disabled="true"<?php
			} ?> class="form-control" data-search-select-default-column="<?=$type?>" id="fSearchDefaultSort" name="fSearchDefaultSort">
				<?php foreach ($all->getSortableColumns() as $col) {
					?>
					<option id="<?=$col->getColumnKey()?>" value="<?=$col->getColumnKey()?>" <?php if ($col->getColumnKey() == $ds->getColumnKey()) {
						?> selected="true" <?php
					}
					?>><?=$col->getColumnName()?></option>
					<?php
				} ?>
			</select>
		</div>

		<div class="form-group">
			<label class="control-label" for="fSearchDefaultSortDirection"><?=t('Direction')?></label>
			<select <?php if (count($all->getSortableColumns()) == 0) {
					?>disabled="true"<?php
			} ?> class="form-control" data-search-select-default-column-direction="<?=$type?>"
					name="fSearchDefaultSortDirection">
				<option value="asc" <?php if (is_object($ds) && $ds->getColumnDefaultSortDirection() == 'asc') {
					?> selected="true" <?php
				} ?>><?=t('Ascending')?></option>
				<option value="desc" <?php if (is_object($ds) && $ds->getColumnDefaultSortDirection() == 'desc') {
					?> selected="true" <?php
				} ?>><?=t('Descending')?></option>
			</select>
		</div>

	</fieldset>

</section>

<script type="text/javascript">
	$(function() {
		var $form = $('section[data-section=customize-results]'),
			$columns = $form.find('ul[data-search-column-list]');

		$('ul[data-search-column-list]').sortable({
			cursor: 'move',
			opacity: 0.5
		});
		$form.on('click', 'input[type=checkbox]', function() {
			var label = $(this).parent().find('span').html(),
				id = $(this).attr('id');

			if ($(this).prop('checked')) {
				if ($form.find('li[data-field-order-column=\'' + id + '\']').length == 0) {
					$columns.append('<li data-field-order-column="' + id + '"><input type="hidden" name="column[]" value="' + id + '" />' + label + '<\/li>');
				}
			} else {
				$columns.find('li[data-field-order-column=\'' + id + '\']').remove();
			}
		});

	});
</script>