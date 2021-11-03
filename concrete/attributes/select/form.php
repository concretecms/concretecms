<?php defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use HtmlObject\Element;

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
$options = [];

// Checkbox list.
if ($akSelectAllowMultipleValues && !$akSelectAllowOtherValues) {
    $options = $controller->getOptions();
    foreach ($options as $opt) {
        ?>

        <div class="form-check">
            <?=$form->checkbox($view->field('atSelectOptionValue') . '[]', $opt->getSelectAttributeOptionID(), in_array($opt->getSelectAttributeOptionID(), $selectedOptionIDs));
        ?>
            <label class="form-check-label">
                <?=$opt->getSelectAttributeOptionDisplayValue()?>
            </label>
        </div>


    <?php
    }
}

// Select Menu.
if (!$akSelectAllowMultipleValues && !$akSelectAllowOtherValues && !$akDisplayMultipleValuesOnSelect) {
    if (!$akHideNoneOption) {
        $options = ['' => t('** None')];
    }
    foreach ($controller->getOptions() as $option) {
        $options[$option->getSelectAttributeOptionID()] = $option->getSelectAttributeOptionDisplayValue();
    }
    ?>
    <?=$form->select($view->field('atSelectOptionValue'), $options, empty($selectedOptionIDs) ? '' : $selectedOptionIDs[0]);
    ?>


<?php
}

// Radio list.
if (!$akSelectAllowMultipleValues && !$akSelectAllowOtherValues && $akDisplayMultipleValuesOnSelect) {
    $index = 1;
    if (!$akHideNoneOption) {
        ?>
        <div class="form-check">
                <?= $form->radio($view->field('atSelectOptionValue'), '', empty($selectedOptionIDs) ? '' : $selectedOptionIDs[0]) ?>
                <?= $form->label($view->field('atSelectOptionValue').$index,  t('None'), ['class' => 'form-check-label'] )?>

        </div>

        <?php
        $index++;
    }

    foreach ($controller->getOptions() as $opt) { ?>

        <div class="form-check">
                <?=$form->radio($view->field('atSelectOptionValue'), $opt->getSelectAttributeOptionID(), in_array($opt->getSelectAttributeOptionID(), $selectedOptionIDs));
                ?>
                <?= $form->label($view->field('atSelectOptionValue').$index,  $opt->getSelectAttributeOptionDisplayValue(), ['class' => 'form-check-label'])?>
        </div>

    <?php
    $index++;}
}

// Select2
if ($akSelectAllowOtherValues) {
    $options = [];

    if (is_array($selectedOptionIDs) && count($selectedOptionIDs)) {
        $optionsList = $controller->getOptions();
        foreach ($optionsList as $opt) {
            if (in_array('SelectAttributeOption:' . $opt->getSelectAttributeOptionID(), $selectedOptionIDs)) {
                $options['SelectAttributeOption:' . $opt->getSelectAttributeOptionID()] = $opt->getSelectAttributeOptionDisplayValue(true);
            }
        }
    }


    /*
       Note: the form-control on here is NOT ideal, that's bootstrap 4 markup,
       but bootstrap select doesn't understand form-select so if you don't give it form-control you won't get full width form controls here
    */
    echo (string) new Element(
        'span',
        $form->selectMultiple($view->field('atSelectOptionValue'), $options, count($selectedOptionIDs) ? $selectedOptionIDs : '', ['class' => 'form-control', 'data-select-and-add' => $akID]),
        [
            'class' => 'ccm-select-values-selector',
            'id' => 'ccm-select-values-selector-' . $akID,
        ]
    );
    ?>
    <script type="text/javascript">
        $(function() {
            $('select[data-select-and-add="<?=$akID?>"]').selectpicker(
                {
                    liveSearch: true,
                    allowAdd: true,
                }
            ).ajaxSelectPicker(
                {
                    ajax: {
                        url: "<?=$view->action('load_autocomplete_values'); ?>",
                        data: {
                            term: "{{{q}}}"
                        },
                    },
                    locale: {
                        currentlySelected: "<?=t('Currently Selected'); ?>",
                        emptyTitle: "<?=t('Select and begin typing'); ?>",
                        errorText: "<?=t('Unable to retrieve results'); ?>",
                        searchPlaceholder: "<?=t('Search...'); ?>",
                        statusInitialized: "<?=t('Start typing a search query'); ?>",
                        statusNoResults: "<?=t('No Results'); ?>",
                        statusSearching: "<?=t('Searching...'); ?>",
                        statusTooShort: "<?=t('Please enter more characters'); ?>",
                    },
                    preserveSelected: true,
                    clearOnEmpty: false,
                    minLength: 2,
                },
            );
        });
    </script>

<?php
}
