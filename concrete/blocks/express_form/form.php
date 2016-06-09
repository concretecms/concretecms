<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php echo Loader::helper('concrete/ui')->tabs(array(
    array('form-add', t('Add'), true),
    array('form-edit', t('Edit')),
    array('form-results', t('Results')),
    array('form-options', t('Options')),
));?>

<div id="ccm-tab-content-form-add" class="ccm-tab-content" data-action="<?=$view->action('add_control')?>">
    <div class="alert alert-success" style="display: none"><?=t('Question added successfully.')?></div>
    <fieldset>
        <legend><?php echo t('New Question')?></legend>

        <div data-view="add-question-inner">

        </div>

        <div class="form-group">

            <button type="button" class="btn btn-primary" data-action="add-question"><?=t('Add Question')?></button>
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


<div id="ccm-tab-content-form-results" class="ccm-tab-content" >
    <fieldset>
        <input type="hidden" name="resultsFolder" value="<?=$resultsFolder?>">

        <legend><?php echo t('Results')?></legend>
        <p><?=t('Store results in a folder:')?></p>

        <?php if (is_object($tree)) {
        ?>
        <div data-root-tree-node-id="<?=$formResultsRootFolderNodeID?>" data-tree="<?=$tree->getTreeID()?>">
        </div>
        <?php } ?>


    </fieldset>

</div>

<div id="ccm-tab-content-form-options" class="ccm-tab-content">
    <fieldset>
        <legend><?=t('Options')?></legend>
        <div class="form-group">
            <?=$form->label('formName', t('Form Name'))?>
            <?=$form->text('formName', $formName)?>
        </div>
        <div class="form-group">
            <?=$form->label('submitLabel', t('Submit Button Label'))?>
            <?=$form->text('submitLabel', $submitLabel)?>
        </div>
        <div class="form-group">
            <?=$form->label('thankyouMsg', t('Message to display when completed'))?>
            <?=$form->textarea('thankyouMsg', $thankyouMsg, array('rows' => 3))?>
        </div>
        <div class="form-group">
            <?=$form->label('recipientEmail', t('Notify me by email when people submit this form'))?>
            <div class="input-group">
				<span class="input-group-addon" style="z-index: 2000">
				<?=$form->checkbox('notifyMeOnSubmission', 1, $notifyMeOnSubmission == 1)?>
				</span><?=$form->text('recipientEmail', $recipientEmail, array('style' => 'z-index:2000;'))?>
            </div>
            <span class="help-block"><?=t('(Seperate multiple emails with a comma)')?></span>
        </div>
        <div data-view="form-options-email-reply-to"></div>
        <div class="form-group">
            <label class="control-label"><?=t('Solving a <a href="%s" target="_blank">CAPTCHA</a> Required to Post?', t('http://en.wikipedia.org/wiki/Captcha'))?></label>
            <div class="radio">
                <label>
                    <?=$form->radio('displayCaptcha', 1, (int) $displayCaptcha)?>
                    <span><?=t('Yes')?></span>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?=$form->radio('displayCaptcha', 0, (int) $displayCaptcha)?>
                    <span><?=t('No')?></span>
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="ccm-form-redirect"><?=t('Redirect to another page after form submission?')?></label>
            <div id="ccm-form-redirect-page">
                <?php
                $page_selector = Loader::helper('form/page_selector');
                if ($redirectCID) {
                    echo $page_selector->selectPage('redirectCID', $redirectCID);
                } else {
                    echo $page_selector->selectPage('redirectCID');
                }
                ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="ccm-form-fileset"><?=t('Add uploaded files to a set?')?></label>
                <?php

                $fileSets = Concrete\Core\File\Set\Set::getMySets();
                $sets = array(0 => t('None'));
                foreach ($fileSets as $fileSet) {
                    $sets[$fileSet->getFileSetID()] = $fileSet->getFileSetDisplayName();
                }
                echo $form->select('addFilesToSet', $sets, $addFilesToSet);
                ?>
        </div>

    </fieldset>
</div>

<script type="text/template" data-template="express-form-form-control">
<li class="list-group-item"
    data-action="<?=$view->action('get_control')?>"
    data-form-control-attribute-type="<%=control.attributeType%>"
    data-form-control-label="<%=control.displayLabel%>"
    data-form-control-id="<%=control.id%>">
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

<script type="text/template" data-template="express-form-reply-to-email">
    <div class="form-group">
        <?=$form->label('replyToEmailControlID', t('Set value of Reply-To to Email Field'))?>
        <select name="replyToEmailControlID" class="form-control">
            <option value=""><?=t('** None')?></option>
            <% _.each(controls, function(control){ %>
            <option value="<%=control.key%>" <% if (selected == control.key) { %>selected<% } %>><%=_.escape(control.value)%></option>
            <% }); %>
        </select>
    </div>
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
    Concrete.event.publish('open.block_express_form', {
        controls: <?=json_encode($controls)?>,
        types: <?=json_encode($types_select)?>,
        settings: {
            'replyToEmailControlID': <?=json_encode($replyToEmailControlID)?>
        }
    });
</script>