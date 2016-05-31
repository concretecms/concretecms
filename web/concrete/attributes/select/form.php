<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Checkbox list.
 */
if ($akSelectAllowMultipleValues && !$akSelectAllowOtherValues) {

	$form = Loader::helper('form');
	$options = $controller->getOptions();
	foreach($options as $opt) { ?>

		<div class="checkbox"><label>
				<?=$form->checkbox($view->field('atSelectOptionValue') . '[]', $opt->getSelectAttributeOptionID(), in_array($opt->getSelectAttributeOptionID(), $selectedOptions)); ?>
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
	foreach($controller->getOptions() as $option) {
		$options[$option->getSelectAttributeOptionID()] = $option->getSelectAttributeOptionDisplayValue();
	}
	?>
	<?=$form->select($view->field('atSelectOptionValue'), $options, $selectedOptions[0]); ?>


<? }

/**
 * Select2
 */
if ($akSelectAllowOtherValues) {

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$value = $controller->request('atSelectOptionValue');
	} else {
		$values = array();
		foreach($selectedOptions as $optionID) {
			$values[] = 'SelectAttributeOption:' . $optionID;
		}
		$value = implode(\Config::get('app.attributes.select.multiple.separator'), $values);
	}


	?>
	<input type="hidden" data-select-and-add="<?=$akID?>" style="width: 100%" name="<?=$view->field('atSelectOptionValue')?>" value="<?=$value?>" />
	<script type="text/javascript">
		$(function() {
			$('input[data-select-and-add=<?=$akID?>]').select2({
				tags: true,
				createSearchChoicePosition: 'bottom',
				initSelection: function(element, callback) {
					var data = [];
					$.ajax({
						'dataType': 'json',
						'data': {value: $(element).val()},
						'url': '<?=$view->action('load_autocomplete_selected_value')?>'
					}).done(function(data) {
						callback(data);
					});

					callback(data);
				},
				createSearchChoice: function(term, data) {
					if ($(data).filter(function() {
							return this.text.localeCompare(term) === 0;
						}).length === 0) {
						return {
							id: term,
							text: term
						};
					}
				},
				<? if ($akSelectAllowMultipleValues) { ?>
					tokenSeparators: ['<?php echo \Config::get('app.attributes.select.multiple.separator'); ?>'],
					separator: "<?php echo \Config::get('app.attributes.select.multiple.separator'); ?>",
					multiple: true,
				<? } else { ?>
					maximumSelectionSize: 1,
				<? } ?>
				minimumInputLength: 1,
				ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
					url: "<?=$view->action('load_autocomplete_values')?>",
					dataType: 'json',
					quietMillis: 250,
					data: function (term, page) {
						return {
							q: term, // search term
						};
					},
					results: function (data, page) {
					// parse the results into the format expected by Select2.
						// since we are using custom formatting functions we do not need to alter the remote JSON data
						return { results: data };
					},
				}

			});
		});
	</script>

<? }