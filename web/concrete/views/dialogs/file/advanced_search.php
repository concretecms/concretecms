<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui ccm-search-fields-advanced-dialog">

    <?php echo Core::make('helper/concrete/ui')->tabs(array(
        array('fields', t('Search Fields'), true),
        array('columns', t('Customize Results'))
    ));?>

    <div class="ccm-tab-content" id="ccm-tab-content-fields">

        <form class="ccm-search-fields ccm-search-fields-none" data-search-form="files" method="post" action="<?php echo URL::to('/ccm/system/search/files/submit')?>">
            <button class="btn btn-primary" type="button" data-button-action="add-field"><?=t('Add Field')?></button>
            <hr/>
            <div data-container="search-fields" class="ccm-search-fields-advanced">

            </div>
        </form>
    </div>

    <div class="ccm-tab-content" id="ccm-tab-content-columns">
        Columns
    </div>


    <div class="dialog-buttons">
        <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
        <button type="button" onclick="$('form[data-search-form=files]').trigger('submit')" class="btn btn-primary pull-right"><?=t('Search')?></button>
        <button type="button" class="btn btn-success pull-right"><?=t('Save as Search Preset')?></button>
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
