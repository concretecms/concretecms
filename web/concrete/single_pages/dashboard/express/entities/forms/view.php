<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-dashboard-header-buttons btn-group">
    <a href="<?=URL::to('/dashboard/express/entities/forms', $entity->getID())?>" class="btn btn-default"><?=t("Back to Object")?></a>
    <a href="<?=URL::to('/dashboard/express/entities/forms', 'edit', $expressForm->getID())?>" class="btn btn-default"><?=t("Edit Details")?></a>
    <button type="button" class="btn btn-default" data-dialog="add-set"><?= t('Add Field Set') ?></button>
    <button type="button" class="btn btn-danger" data-dialog="delete-form"><?= t('Delete Form') ?></button>
</div>

<div style="display: none">
    <div id="ccm-dialog-delete-form" class="ccm-ui">
        <form method="post" action="<?=$view->action('delete_form', $entity->getID())?>">
            <?=Core::make("token")->output('delete_form')?>
            <input type="hidden" name="form_id" value="<?=$expressForm->getID()?>">
            <p><?=t('Are you sure you want to delete this form? This cannot be undone.')?></p>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-danger pull-right" onclick="$('#ccm-dialog-delete-form form').submit()"><?=t('Delete Form')?></button>
            </div>
        </form>
    </div>

    <div id="ccm-dialog-add-set" class="ccm-ui">
        <form method="post" action="<?=$view->action('add_set', $expressForm->getID())?>">
            <?=Loader::helper('validation/token')->output('add_set')?>
            <div class="form-group">
                <?=$form->label('name', tc('Name of a set', 'Set Name'))?>
                <?=$form->text('name')?>
            </div>
        </form>
        <div class="dialog-buttons">
            <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
            <button class="btn btn-primary pull-right" onclick="$('#ccm-dialog-add-set form').submit()"><?=t('Add Set')?></button>
        </div>
    </div>

</div>

<p class="lead"><?php echo $expressForm->getName(); ?></p>

<?php if (count($fieldSets)) {
    ?>

    <?php foreach($fieldSets as $set) { ?>

        <div class="panel panel-default" data-field-set="<?=$set->getID()?>">
            <div class="panel-heading">
                <? /*
                <ul class="ccm-page-type-composer-item-controls">
                    <li><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/page_types/composer/form/add_control?ptComposerFormLayoutSetID=<?=$set->getPageTypeComposerFormLayoutSetID()?>" dialog-title="<?=t('Add Form Control')?>" dialog-width="640" dialog-height="400" data-command="add-form-set-control"><i class="fa fa-plus"></i></a></li>
                    <li><a href="#" data-command="move_set" style="cursor: move"><i class="fa fa-arrows"></i></a></li>
                    <li><a href="#" data-edit-set="<?=$set->getPageTypeComposerFormLayoutSetID()?>"><i class="fa fa-pencil"></i></a></li>
                    <li><a href="#" data-delete-set="<?=$set->getPageTypeComposerFormLayoutSetID()?>"><i class="fa fa-trash-o"></i></a></li>
                </ul>
                <div class="ccm-page-type-composer-form-layout-control-set-name" ><?
                    if ($set->getPageTypeComposerFormLayoutSetDisplayName()) {
                        echo $set->getPageTypeComposerFormLayoutSetDisplayName();
                    } else {
                        echo t('(No Name)');
                    }
                    ?>
                </div>
                */ ?>

                <div data-label="field-set"><?=$set->getTitle()?></div>

            </div>

            <table class="table table-hover" style="width: 100%;">
                <tbody>

                </tbody>
            </table>

        </div>

    <? } ?>

<?php
} else {
    ?>
    <p><?=t('You have not created any field sets.')?></p>
<?php
} ?>


<script type="text/javascript">
    $(function() {
        $('button[data-dialog]').on('click', function() {
            var $element = $('#ccm-dialog-' + $(this).attr('data-dialog'));
            jQuery.fn.dialog.open({
                element: $element,
                modal: true,
                width: 320,
                title: $(this).text(),
                height: 'auto'
            });
        });
    });
</script>

