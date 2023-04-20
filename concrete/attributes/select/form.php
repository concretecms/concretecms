<?php defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Attribute\Component\OptionSelectInstanceFactory;

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
            <label class="form-check-label" for="<?= $view->field('atSelectOptionValue') . '_' . $opt->getSelectAttributeOptionID(); ?>">
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
                $options[] = 'SelectAttributeOption:' . $opt->getSelectAttributeOptionID();
            }
        }
    }

    /**
     * @var $view \Concrete\Core\Attribute\View
     */
    $key = $view->getAttributeKey();
    $factory = app(OptionSelectInstanceFactory::class);
    $instance = $factory->createInstance($key);

    if ($akSelectAllowMultipleValues) {
        $inputName = $view->field('atSelectOptionValue') . '[]';
    } else {
        $inputName = $view->field('atSelectOptionValue');
    }

    ?>

    <div data-vue="cms">
        <concrete-option-select
            input-name="<?=$inputName?>"
            data-source-url="<?=$instance->getDataSourceUrl()?>"
            selected-options-url="<?=$instance->getSelectedOptionsUrl()?>"
            attribute-key-id="<?=$key->getAttributeKeyID()?>"
            access-token="<?=$instance->getAccessToken()?>"
            :allow-multiple-values="<?=$akSelectAllowMultipleValues ? 'true' : 'false'?>"
            :value='<?=json_encode($options)?>'
        ></concrete-option-select>
    </div>


<?php
}
