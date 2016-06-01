<?php defined('C5_EXECUTE') or die("Access Denied.");

/*
 * Checkbox list.
 */
if ($akSelectAllowMultipleValues && !$akSelectAllowOtherValues) {
    $form = Loader::helper('form');
    $options = $controller->getOptions();
    foreach ($options as $opt) {
        ?>

		<div class="checkbox"><label>
				<?=$form->checkbox($view->field('atSelectOptionValue') . '[]', $opt->getSelectAttributeOptionID(), in_array($opt->getSelectAttributeOptionID(), $selectedOptions));
        ?>
				<?=$opt->getSelectAttributeOptionDisplayValue()?>
			</label>
		</div>


	<?php 
    }
}

/*
 * Select Menu.
 */
if (!$akSelectAllowMultipleValues && !$akSelectAllowOtherValues) {
    $form = Loader::helper('form');
    $options = array('' => t('** None'));
    foreach ($controller->getOptions() as $option) {
        $options[$option->getSelectAttributeOptionID()] = $option->getSelectAttributeOptionDisplayValue();
    }
    ?>
	<?=$form->select($view->field('atSelectOptionValue'), $options, $selectedOptions[0]);
    ?>


<?php 
}

/*
 * Select2
 */
if ($akSelectAllowOtherValues) {
	/*
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $value = $controller->request('atSelectOptionValue');
    } else {
        $values = array();
        foreach ($selectedOptions as $optionID) {
            $values[] = 'SelectAttributeOption:' . $optionID;
        }
        $value = implode(',', $values);
    }
	*/

	print $value;

    ?>
	<input type="hidden" data-select-and-add="<?=$akID?>" style="width: 100%" name="<?=$view->field('atSelectOptionValue')?>" value="<?=$value?>" />
	<script type="text/javascript">
		$(function() {
			$('input[data-select-and-add=<?=$akID?>]').selectize({
				valueField: 'id',
				labelField: 'text',
				options: <?=json_encode($selectedOptions)?>,
				items: <?=json_encode($selectedOptionIDs)?>,
				openOnFocus: false,
				create: true,
				createFilter: function(input) {
					return input.length >= 1;
				},

				maxOptions: 10,

				<?php if ($akSelectAllowMultipleValues) {
    ?>
					delimiter: ',',
					maxItems: 500,
				<?php 
} else {
    ?>
					maxItems: 1,
				<?php 
}
    ?>
				load: function(query, callback) {
					if (!query.length) return callback();
					$.ajax({
						url: "<?=$view->action('load_autocomplete_values')?>",
						dataType: 'json',
						error: function() {
							callback();
						},
						success: function(res) {
							callback(res);
						}
					});
				}
			});
		});
	</script>

<?php 
}
