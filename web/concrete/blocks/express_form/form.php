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

        <div data-view="add-question-inner">

        </div>

        <div class="form-group">
            <hr/>
            <button type="button" class="btn btn-primary pull-right" data-action="add-question"><?=t('Add Question')?></button>
        </div>

    </fieldset>

</div>

<div id="ccm-tab-content-form-edit" class="ccm-tab-content" data-action="<?=$view->action('update_control')?>">

    <div class="alert alert-success" style="display: none"><?=t('Question updated successfully.')?></div>

    <div data-view="form-fields">

    <fieldset>
        <legend><?php echo t('Fields')?></legend>

        <ul class="list-group">
        </ul>
    </fieldset>

    </div>

    <div data-view="edit-question" style="display: none">

        <fieldset>
            <legend><?php echo t('Edit Question')?></legend>

            <div data-view="edit-question-inner">

            </div>

            <div class="form-group">
                <hr/>
                <button type="button" class="btn btn-default" data-action="cancel-edit"><?=t('Cancel')?></button>
                <button type="button" class="btn btn-primary pull-right" data-action="update-question"><?=t('Save Question')?></button>
            </div>

        </fieldset>

    </div>



</div>

<script type="text/template" data-template="express-form-form-control">
<li class="list-group-item" data-action="<?=$view->action('get_control')?>" data-form-control-id="<%=control.id%>">
    <input type="hidden" name="controlID[]" value="<%=control.id%>">
    <%=control.displayLabel%>
    <span class="pull-right">
        <i style="cursor: move" class="fa fa-arrows"></i>
        <a href="javascript:void(0)" class="icon-link" data-action="edit-control"><i class="fa fa-pencil"></i></a>
        <a href="javascript:void(0)" class="icon-link" data-action="delete-control"><i class="fa fa-trash"></i></a>
        </span>
    <% if (control.isRequired) { %>
    <span style="margin-right: 20px" class="badge badge-info"><?=t('Required')?></span>
    <% } %>
</li>
</script>

<script type="text/template" data-template="express-form-form-question">

    <% if (id) { %>
        <input type="hidden" name="id" value="<%=id%>">
    <% } %>

    <div class="form-group">
        <?=$form->label('question', t('Question'))?>
        <input type="text" name="question" class="form-control" maxlength="255" value="<%=question%>">
    </div>

    <div class="form-group" data-action="<?=$view->action('get_type_form')?>" data-group="attribute-types">
        <?=$form->label('type', t('Answer Type'))?>
        &nbsp; <i class="fa fa-refresh fa-spin" style="display: none"></i>
        <select name="type" class="form-control">
        <% _.each(types, function(type){ %>
            <option value="<%=type.id%>" <% if (selectedType == type.id) { %>selected<% } %>><%=_.escape(type.displayName)%></option>
        <% }); %>
        </select>

    </div>

    <% if (typeContent) { %>
        <div data-group="attribute-type-data"><%=typeContent%></div>
    <% } else { %>
        <div style="display: none" data-group="attribute-type-data"></div>
    <% } %>

    <div class="form-group">
        <label class="control-label"><?=t('Required')?></label>
        <div class="radio"><label>
            <input type="radio" name="required<% if (id) { %>Edit<% } %>" value="1" <% if (isRequired) { %>checked<% } %>>
            <?=t('Yes')?>
        </label></div>
        <div class="radio"><label>
            <input type="radio" name="required<% if (id) { %>Edit<% } %>" value="0" <% if (!isRequired) { %>checked<% } %>>
            <?=t('No')?>
        </label></div>
    </div>
</script>


<script type="application/javascript">
    Concrete.event.publish('block.express_form.open', {
        controls: <?=json_encode($controls)?>,
        types: <?=json_encode($types_select)?>
    });
</script>