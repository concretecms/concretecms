<?php defined('C5_EXECUTE') or die('Access Denied.');
$color = \Core::make('helper/form/color');
?>

<fieldset>
    <legend><?=t('Data Source')?></legend>
    <?php View::element('calendar/block/data_source', ['caID' => isset($caID) ? $caID : null, 'calendarAttributeKeyHandle' => isset($calendarAttributeKeyHandle) ? $calendarAttributeKeyHandle : null]) ?>
</fieldset>

<fieldset>
    <legend><?=t('View Options')?></legend>
    <div data-section="customize-results">
        <div class="form-group">
            <label class="control-label"><?= t('View Types'); ?></label>
            <?php
            if ($viewTypes) {
                foreach ($viewTypes as $key => $name) { ?>
                    <div class="checkbox">
                        <label>
                            <?= $form->checkbox('viewTypes[]', $key, in_array($key, $viewTypesSelected)); ?>
                            <span><?= $name; ?></span>
                        </label>
                    </div>
                <?php
                }
            }
            ?>
        </div>

        <div class="form-group">
            <label class="control-label"><?= t('View Type Order'); ?></label>
            <p class="help-block"><?= t('Click and drag to change view type order.'); ?></p>
            <ul class="item-select-list" data-sort-list="view-types">
                <?php
                if ($viewTypesOrder) {
                    foreach ($viewTypesOrder as $valueName) {
                        $valueNameArray = explode('_', $valueName);
                        ?>
                        <li style="cursor: move" data-field-order-item="<?= $valueNameArray[0]; ?>">
                            <input type="hidden" name="viewTypesOrder[]" value="<?= $valueName; ?>"><?= $valueNameArray[1]; ?>
                            <i class="ccm-item-select-list-sort ui-sortable-handle"></i>
                        </li>
                    <?php
                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="form-group">
        <?= $form->label('defaultView', t('Default View')); ?>
        <?= $form->select('defaultView', $viewTypes, isset($defaultView) ? $defaultView : null); ?>
    </div>

    <div class="form-group">
        <label class="control-label"><?= t('Day Heading Links'); ?></label>
        <div class="checkbox">
            <label>
                <?= $form->checkbox('navLinks', 1, !empty($navLinks)); ?>
                <?= t('Make day headings into links.'); ?>
            </label>
            <p class="help-block"><?= t('When clicked, day heading links go to the view that represents the day.'); ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label"><?= t('Event Limit'); ?></label>
        <div class="checkbox">
            <label>
                <?= $form->checkbox('eventLimit', 1, !empty($eventLimit)); ?>
                <?= t('Limit the number of events displayed on a day.'); ?>
            </label>
            <p class="help-block"><?= t('When there are too many events, an "+X more" link is displayed.'); ?></p>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend><?=t('Filtering')?></legend>
    <div class="form-group">
        <label class="control-label" for="totalToRetrieve"><?= t('Filter by Topic Attribute') ?></label>
        <select class="form-control" name="filterByTopicAttributeKeyID">
            <option value=""><?=t('** None')?></option>
            <?php foreach ($attributeKeys as $ak) {
                $attributeController = $ak->getController();
                ?>
                <option value="<?=$ak->getAttributeKeyID()?>" <?php if (isset($filterByTopicAttributeKeyID) && $ak->getAttributeKeyID() == $filterByTopicAttributeKeyID) { ?>selected<?php } ?>
                    data-tree-id="<?=$attributeController->getTopicTreeID()?>"><?=$ak->getAttributeKeyDisplayName()?></option>
            <?php
            } ?>
        </select>
        <input type="hidden" name="filterByTopicID" value="<?=isset($filterByTopicID) ? $filterByTopicID : ''?>">
        <div class="tree-view-container">
            <div class="tree-view-template">
            </div>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend><?=t('Lightbox')?></legend>
    <div class="alert alert-info"><?=t('Check any properties that you wish to display in a lightbox. Check none to disable the lightbox.')?></div>
    <?php foreach ($lightboxProperties as $key => $name) { ?>
        <div class="checkbox">
            <label>
                <?=$form->checkbox('lightboxProperties[]', $key, in_array($key, $lightboxPropertiesSelected))?>
                <?=$name?>
            </label>
        </div>
    <?php
    } ?>
</fieldset>

<script>
    $(function() {
        var treeViewTemplate = $('.tree-view-template');
        $('select[name=filterByTopicAttributeKeyID]').on('change', function() {
            var chosenTree = $(this).find('option:selected').attr('data-tree-id');
            $('.tree-view-template').remove();
            if (!chosenTree) {
                return;
            }
            $('.tree-view-container').append(treeViewTemplate);

            $('.tree-view-template').concreteTree({
                'treeID': chosenTree,
                'chooseNodeInForm': true,
                'selectNodesByKey': [<?=isset($filterByTopicID) ? (int) $filterByTopicID : 0?>],
                'onSelect' : function(nodes) {
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
        form.on('click', 'input[type=checkbox]', function() {
            var label = $(this).parent().find('span').html();
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
