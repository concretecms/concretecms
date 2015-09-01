<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Checkbox list.
 */
if ($akSelectAllowMultipleValues && !$akSelectAllowOtherValues) {

	$form = Loader::helper('form');
	$options = $this->controller->getOptions();
	foreach($options as $opt) { ?>

		<div class="checkbox"><label>
				<?=$form->checkbox($view->field('atSelectOptionValue'), $opt->getSelectAttributeOptionID(), in_array($opt->getSelectAttributeOptionID(), $selectedOptions)); ?>
				<?=$opt->getSelectAttributeOptionDisplayValue()?>
			</label>
		</div>


	<? }


}

/**
 * Select Menu.
 */
if (!$akSelectAllowMultipleValues && !$akSelectAllowOtherValues) {

	$form = Loader::helper('form');
	$options = array('' => t('** None'));
	foreach($this->controller->getOptions() as $option) {
		$options[$option->getSelectAttributeOptionID()] = $option->getSelectAttributeOptionDisplayValue();
	}
	?>
	<?=$form->select($view->field('atSelectOptionValue'), $options, $selectedOptions[0]); ?>


<? }

/**
 * Select Menu + Add One.
 */
if (!$akSelectAllowMultipleValues && $akSelectAllowOtherValues) {
	$tags = array();
	foreach($this->controller->getOptions() as $option) {
		$tag = new stdClass;
		$tag->id = 'SelectAttributeOption:' . $option->getSelectAttributeOptionID();
		$tag->text = $option->getSelectAttributeOptionDisplayValue();
		$tags[] = $tag;
		$values[] = $tag->id;
	}
	$value = implode(',', $values);
	?>

	<input type="hidden" data-select-and-add="<?=$akID?>" style="width: 100%" name="<?=$view->field('atSelectOptionValue')?>" value="<?=$value?>" />
	<script type="text/javascript">
		$(function() {
			$('input[data-select-and-add=<?=$akID?>]').select2({
				tags: <?=json_encode($tags)?>,
				maximumSelectionSize: 1
			});
		});
	</script>

<? }

/**
 * Select Multiple + Add Multiple.
 */
if ($akSelectAllowMultipleValues && $akSelectAllowOtherValues) {
	$tags = array();
	foreach($this->controller->getOptions() as $option) {
		$tag = new stdClass;
		$tag->id = 'SelectAttributeOption:' . $option->getSelectAttributeOptionID();
		$tag->text = $option->getSelectAttributeOptionDisplayValue();
		$tags[] = $tag;
		$values[] = $tag->id;
	}
	$value = implode(',', $values);
	?>

	<input type="hidden" data-select-and-add="<?=$akID?>" style="width: 100%" name="<?=$view->field('atSelectOptionValue')?>" value="<?=$value?>" />
	<script type="text/javascript">
		$(function() {
			$('input[data-select-and-add=<?=$akID?>]').select2({
				tags: <?=json_encode($tags)?>,
				tokenSeparators: [',']

			});
		});
	</script>

<? }