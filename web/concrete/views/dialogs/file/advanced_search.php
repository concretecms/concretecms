<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui ccm-search-fields-advanced-dialog">

    <?php echo Core::make('helper/concrete/ui')->tabs(array(
        array('fields', t('Search Fields'), true),
        array('columns', t('Customize Results'))
    ));?>

    <form class="ccm-search-fields ccm-search-fields-none" data-advanced-search-form="files" method="post" action="<?=$controller->action('submit')?>">

    <div class="ccm-tab-content" id="ccm-tab-content-fields">

            <button class="btn btn-primary" type="button" data-button-action="add-field"><?=t('Add Field')?></button>
            <hr/>
            <div data-container="search-fields" class="ccm-search-fields-advanced">

            </div>
    </div>

    <div class="ccm-tab-content" id="ccm-tab-content-columns">
        <?php
        print $customizeElement->render();
        ?>
    </div>
    </form>


    <div class="dialog-buttons">
        <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
        <button type="button" onclick="$('form[data-advanced-search-form=files]').trigger('submit')" class="btn btn-primary pull-right"><?=t('Search')?></button>
        <button type="button" data-button-action="save-search-preset" class="btn btn-success pull-right"><?=t('Save as Search Preset')?></button>
    </div>


</div>

<div style="display: none">
    <div data-dialog="save-search-preset" class="ccm-ui">
        <form data-form="save-preset" action="<?=$controller->action('save_preset')?>" method="post">
            <div class="form-group">
                <?php $form = Core::make('helper/form'); ?>
                <?=$form->label('presetName', t('Name'))?>
                <?=$form->text('presetName')?>
            </div>
        </form>
        <div class="dialog-buttons">
            <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
            <button class="btn btn-primary pull-right" data-button-action="save-search-preset-submit"><?=t('Save Preset')?></button>
        </div>
    </div>
</div>

<script type="text/template" data-template="search-field-row">
    <div class="ccm-search-fields-row">
        <select data-action="<?=$controller->action('add_field')?>" name="field[]" class="ccm-search-choose-field form-control">
            <option value=""><?=t('** Select Field')?></option>
            <?php foreach($manager->getGroups() as $group) { ?>
                <optgroup label="<?=$group->getName()?>">
                    <?php foreach($group->getFields() as $field) { ?>
                        <option value="<?=$field->getKey()?>"><?=$field->getDisplayName()?></option>
                    <?php } ?>
                </optgroup>
            <?php } ?>
        </select>
        <div class="ccm-search-field-content"><% if (typeof(field) != 'undefined') { %><%=field.html%><% } %></div>
        <a data-search-remove="search-field" class="ccm-search-remove-field" href="#"><i class="fa fa-minus-circle"></i></a>
    </div>
</script>
