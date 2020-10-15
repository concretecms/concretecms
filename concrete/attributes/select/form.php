<?php defined('C5_EXECUTE') or die("Access Denied.");
use HtmlObject\Element;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

/*
 * Checkbox list.
 */
if ($akSelectAllowMultipleValues && !$akSelectAllowOtherValues) {
    $options = $controller->getOptions();
    foreach ($options as $opt) {
        ?>

        <div class="checkbox"><label>
                <?=$form->checkbox($view->field('atSelectOptionValue') . '[]', $opt->getSelectAttributeOptionID(), in_array($opt->getSelectAttributeOptionID(), $selectedOptionIDs));
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
if (!$akSelectAllowMultipleValues && !$akSelectAllowOtherValues && !$akDisplayMultipleValuesOnSelect) {
    if (!$akHideNoneOption) {
        $options = array('' => t('** None'));
    }
    foreach ($controller->getOptions() as $option) {
        $options[$option->getSelectAttributeOptionID()] = $option->getSelectAttributeOptionDisplayValue();
    }
    ?>
    <?=$form->select($view->field('atSelectOptionValue'), $options, empty($selectedOptionIDs) ? '' : $selectedOptionIDs[0]);
    ?>


<?php
}

/*
 * Radio list.
 */
if (!$akSelectAllowMultipleValues && !$akSelectAllowOtherValues && $akDisplayMultipleValuesOnSelect) {
    if (!$akHideNoneOption) {
        ?>
        <div class="radio"><label>
                <?= $form->radio($view->field('atSelectOptionValue'), '', empty($selectedOptionIDs) ? '' : $selectedOptionIDs[0]) ?>
                <?= t('None') ?>
            </label>
        </div>

        <?php
    }

    foreach ($controller->getOptions() as $opt) { ?>

        <div class="radio"><label>
                <?=$form->radio($view->field('atSelectOptionValue'), $opt->getSelectAttributeOptionID(), in_array($opt->getSelectAttributeOptionID(), $selectedOptionIDs));
                ?>
                <?=$opt->getSelectAttributeOptionDisplayValue()?>
            </label>
        </div>

    <?php }
}


/*
 * Select2
 */
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

    echo (string) new Element(
        "span",
        $form->selectMultiple($view->field('atSelectOptionValue'), $options, count($selectedOptionIDs) ? $selectedOptionIDs : '', ['data-select-and-add' => $akID]),
        [
            "class" => "ccm-select-values-selector",
            "id" => "ccm-select-values-selector-" . $akID,
        ]
        );
    ?>
    <script type="text/javascript">
        $(function() {
            $('select[data-select-and-add="<?=$akID?>"]').selectpicker(
                {
                    liveSearch: true,
                    selectOnTab: true,
                }
            ).ajaxSelectPicker(
                {
                    ajax: {
                        url: "<?=$view->action('load_autocomplete_values');?>",
                        data: {
                            term: "{{{q}}}"
                        },
                    },
                    locale: {
                        currentlySelected: "<?=t("Currently Selected");?>",
                        emptyTitle: "<?=t("Select and begin typing");?>",
                        errorText: "<?=t("Unable to retrieve results");?>",
                        searchPlaceholder: "<?=t("Search...");?>",
                        statusInitialized: "<?=t("Start typing a search query");?>",
                        statusNoResults: "<?=t("No Results");?>",
                        statusSearching: "<?=t("Searching...");?>",
                        statusTooShort: "<?=t("Please enter more characters");?>",
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
