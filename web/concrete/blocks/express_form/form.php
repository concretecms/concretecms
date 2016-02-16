<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php echo Loader::helper('concrete/ui')->tabs(array(
    array('form-add', t('Add'), true),
    array('form-edit', t('Edit')),
    array('form-preview', t('Preview')),
    array('form-options', t('Options')),
));?>

<div id="ccm-tab-content-form-add" class="ccm-tab-content" data-action="<?=$view->action('add_control')?>">
    <div class="alert alert-success" style="display: none"><?=t('Question added successfully.')?></div>
    <fieldset>
        <legend><?php echo t('New Question')?></legend>

        <div class="form-group">
            <?=$form->label('question', t('Question'))?>
            <?=$form->text('question', array('maxlength' => '255'))?>
        </div>

        <div class="form-group" data-action="<?=$view->action('get_type_form')?>" data-group="attribute-types">
            <?=$form->label('type', t('Answer Type'))?>
            &nbsp; <i class="fa fa-refresh fa-spin" style="display: none"></i>
            <?=$form->select('type', $types_select)?>
        </div>

        <div style="display: none" data-group="attribute-type-data">

        </div>

<?php /*        <?php foreach($types as $id => $type) { ?>
            <div style="display: none" data-attribute-type-id="<?=$id?>">
                <?=$type?>
            </div>
        <?php } ?>
 */ ?>


        <div class="form-group">
            <label class="control-label"><?=t('Required')?></label>
            <div class="radio"><label><?=$form->radio('required', 1)?> <?=t('Yes')?></label></div>
            <div class="radio"><label><?=$form->radio('required', 0)?> <?=t('No')?></label></div>
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-default" data-action="add-question"><?=t('Add Question')?></button>
        </div>

    </fieldset>

</div>

<div id="ccm-tab-content-form-edit" class="ccm-tab-content">
    <fieldset>
        <legend><?php echo t('Fields')?></legend>

        <ul class="list-group">
        </ul>
    </fieldset>
</div>
<script type="application/javascript">
    Concrete.event.publish('block.express_form.open', {
        controls: <?=json_encode($controls)?>,
        controlTemplate: _.template('<li class="list-group-item" data-form-control-id="<%=control.id%>">' +
            '<input type="hidden" name="controlID[]" value="<%=control.id%>">' +
            '<%=control.displayLabel%>' +
            '<span class="pull-right">' +
            '<i style="cursor: move" class="fa fa-arrows"></i> ' +
            '<a href="javascript:void(0)" class="icon-link" data-action="edit-control"><i class="fa fa-pencil"></i></a> ' +
            '<a href="javascript:void(0)" class="icon-link" data-action="delete-control"><i class="fa fa-trash"></i></a>' +
            '</span>' +
            '<% if (control.isRequired) { %>' +
            '<span style="margin-right: 20px" class="badge badge-info"><?=t('Required')?></span>' +
            '<% } %>' +
            '</li>')
    });
</script>
