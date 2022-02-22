<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Widget\Color;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\View\View;

/** @var int|null $caID */
/** @var string|null $calendarAttributeKeyHandle */
/** @var int|null $filterByTopicAttributeKeyID */
/** @var int $filterByTopicID */
$filterByTopicID = $filterByTopicID ?? 0;
/** @var string $viewTypes */
/** @var string $viewTypesOrder */
/** @var string|null $defaultView */
/** @var int $navLinks */
/** @var int $eventLimit */
/** @var array<string,string> $lightboxProperties */
/** @var string[] $viewTypesSelected */
/** @var string[] $viewTypesOrder */
/** @var array<string,string> $lightboxPropertiesSelected */
/** @var Concrete\Core\Entity\Calendar\Calendar[] $calendars */
/** @var Concrete\Core\Entity\Attribute\Key\EventKey[] $attributeKeys */
/** @var array<string, string> $viewTypes */
/** @var Concrete\Core\Form\Service\Form $form */

$app = Application::getFacadeApplication();
/** @var Concrete\Core\Form\Service\Widget\Color $color */
$color = $app->make(Color::class);
?>

<fieldset>
    <legend>
        <?php echo t('Data Source') ?>
    </legend>

    <?php /** @noinspection PhpUnhandledExceptionInspection */
    View::element('calendar/block/data_source', [
        'caID' => $caID ?? null,
        'calendarAttributeKeyHandle' => $calendarAttributeKeyHandle ?? null,
    ]) ?>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('View Options') ?>
    </legend>

    <div data-section="customize-results">
        <div class="form-group">
            <?php echo $form->label('viewTypes', t('View Types')); ?>

            <?php if ($viewTypes) {
                /**
                 * @var string $key
                 * @var string $name
                 */
                foreach ($viewTypes as $key => $name) { ?>
                    <div class="form-check">
                        <?php echo $form->checkbox('viewTypes[]', $key, in_array($key, $viewTypesSelected), ['name' => 'viewTypes[]', 'id' => 'viewTypes_' . $key]); ?>
                        <?php echo $form->label('viewTypes_' . $key, $name, ['class' => 'form-check-label']); ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('', t('View Type Order')); ?>

            <p class="help-block">
                <?php echo t('Click and drag to change view type order.'); ?>
            </p>

            <ul class="item-select-list" data-sort-list="view-types">
                <?php if ($viewTypesOrder) { ?>
                    <?php foreach ($viewTypesOrder as $valueName) { ?>
                        <?php $valueNameArray = explode('_', $valueName); ?>

                        <li style="cursor: move" data-field-order-item="<?php echo $valueNameArray[0]; ?>">
                            <?php echo $form->hidden('viewTypesOrder[]', $valueName); ?>
                            <?php echo $valueNameArray[1]; ?>
                            <i class="ccm-item-select-list-sort ui-sortable-handle"></i>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('defaultView', t('Default View')); ?>
        <?php echo $form->select('defaultView', $viewTypes, $defaultView ?? null); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('', t('Day Heading Links')); ?>

        <div class="form-check">
            <?php echo $form->checkbox('navLinks', '1', !empty($navLinks)); ?>
            <?php echo $form->label('navLinks', t('Make day headings into links.'), ['class' => 'form-check-label']); ?>
        </div>
    </div>

    <p class="help-block">
        <?php echo t('When clicked, day heading links go to the view that represents the day.'); ?>
    </p>

    <div class="form-group">
        <?php echo $form->label('', t('Event Limit')); ?>

        <div class="form-check">
            <?php echo $form->checkbox('eventLimit', '1', !empty($eventLimit)); ?>
            <?php echo $form->label('eventLimit', t('Limit the number of events displayed on a day.'), ['class' => 'form-check-label']); ?>
        </div>

        <p class="help-block">
            <?php echo t('When there are too many events, an "+X more" link is displayed.'); ?>
        </p>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Filtering') ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label('totalToRetrieve', t('Filter by Topic Attribute')); ?>

        <!--suppress HtmlFormInputWithoutLabel -->
        <select class="form-select" name="filterByTopicAttributeKeyID">
            <option value="">
                <?php echo t('** None') ?>
            </option>

            <?php foreach ($attributeKeys as $ak) {
                /** @var \Concrete\Attribute\Topics\Controller $attributeController */
                $attributeController = $ak->getController(); ?>

                <option value="<?php echo h($ak->getAttributeKeyID()) ?>"
                        <?php if (isset($filterByTopicAttributeKeyID) && $ak->getAttributeKeyID() == $filterByTopicAttributeKeyID) { ?>selected<?php } ?>
                        data-tree-id="<?php echo h($attributeController->getTopicTreeID()) ?>">
                    <?php echo $ak->getAttributeKeyDisplayName() ?>
                </option>
            <?php } ?>
        </select>

        <?php echo $form->hidden('filterByTopicID', (string) $filterByTopicID); ?>

        <div class="tree-view-container">
            <div class="tree-view-template"></div>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Lightbox') ?>
    </legend>

    <div class="alert alert-info">
        <?php echo t('Check any properties that you wish to display in a lightbox. Check none to disable the lightbox.') ?>
    </div>

    <?php foreach ($lightboxProperties as $key => $name) { ?>
        <div class="form-check">
            <?php echo $form->checkbox('lightboxProperties[]', $key, in_array($key, $lightboxPropertiesSelected), ['name' => 'lightboxProperties[]', 'id' => 'lightboxProperties_' . $key]) ?>
            <?php echo $form->label('lightboxProperties_' . $key, $name, ['class' => 'form-check-label']) ?>
        </div>
    <?php } ?>
</fieldset>

<!--suppress JSJQueryEfficiency, ES6ConvertVarToLetConst, EqualityComparisonWithCoercionJS -->
<script>
    $(function () {
        var treeViewTemplate = $('.tree-view-template');
        $('select[name=filterByTopicAttributeKeyID]').on('change', function () {
            var chosenTree = $(this).find('option:selected').attr('data-tree-id');
            $('.tree-view-template').remove();
            if (!chosenTree) {
                return;
            }
            $('.tree-view-container').append(treeViewTemplate);

            $('.tree-view-template').concreteTree({
                'treeID': chosenTree,
                'chooseNodeInForm': true,
                'selectNodesByKey': [<?php echo $filterByTopicID ?>],
                'onSelect': function (nodes) {
                    if (nodes.length) {
                        $('input[name=filterByTopicID]').val(nodes[0]);
                    } else {
                        $('input[name=filterByTopicID]').val('');
                    }
                }
            });
        }).trigger('change');

        $('ul[data-sort-list=view-types]').sortable({
            cursor: 'move',
            opacity: 0.5
        });

        var form = $('[data-section=customize-results]');
        var sortList = form.find('ul[data-sort-list=view-types]');

        form.on('click', 'input[type=checkbox]', function () {
            var label = $(this).parent().find('label').html();
            var id = $(this).attr('id');
            var splitID = id.split('_');
            var value = splitID[1];
            if ($(this).prop('checked')) {
                if (form.find('li[data-field-order-item=\'' + value + '\']').length == 0) {
                    sortList.append('<li data-field-order-item="' + value + '"><input type="hidden" name="viewTypesOrder[]" value="' + value + '_' + label + '">' + label + '<i class="ccm-item-select-list-sort ui-sortable-handle"></i><\/li>');
                }
            } else {
                sortList.find('li[data-field-order-item=\'' + value + '\']').remove();
            }
        });
    });
</script>
