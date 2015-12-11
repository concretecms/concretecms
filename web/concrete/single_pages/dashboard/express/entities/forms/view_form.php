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

        <div style="display: none">
            <div id="ccm-dialog-delete-set-<?=$set->getID()?>" class="ccm-ui">
                <form method="post" action="<?=$view->action('delete_set', $expressForm->getID())?>">
                    <?=Core::make("token")->output('delete_set')?>
                    <input type="hidden" name="field_set_id" value="<?=$set->getID()?>">
                    <p><?=t('Are you sure you want to delete this field set? This cannot be undone.')?></p>
                    <div class="dialog-buttons">
                        <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                        <button class="btn btn-danger pull-right" onclick="$('#ccm-dialog-delete-set-<?=$set->getId()?> form').submit()"><?=t('Delete Set')?></button>
                    </div>
                </form>
            </div>

            <div id="ccm-dialog-update-set-<?=$set->getID()?>" class="ccm-ui">
                <form method="post" action="<?=$view->action('update_set', $expressForm->getID())?>">
                    <?=Core::make("token")->output('update_set')?>
                    <input type="hidden" name="field_set_id" value="<?=$set->getID()?>">
                    <div class="form-group">
                        <?=$form->label('name', tc('Name of a set', 'Set Name'))?>
                        <?=$form->text('name', $set->getTitle())?>
                    </div>
                </form>
                <div class="dialog-buttons">
                    <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                    <button class="btn btn-primary pull-right" onclick="$('#ccm-dialog-update-set-<?=$set->getID()?> form').submit()"><?=t('Update Set')?></button>
                </div>
            </div>

        </div>


        <div class="ccm-item-set panel panel-default" data-field-set="<?=$set->getID()?>">
            <div class="panel-heading">
                <ul class="ccm-item-set-controls">
                    <li><a href="<?=$view->action('add_control', $set->getID())?>" class="dialog-launch" dialog-title="<?=t('Add Form Control')?>" dialog-width="640" dialog-height="400" data-command="add-form-set-control"><i class="fa fa-plus"></i></a></li>
                    <li><a href="#" data-command="move-set" style="cursor: move"><i class="fa fa-arrows"></i></a></li>
                    <li><a href="#" data-dialog="update-set-<?=$set->getId()?>" data-dialog-title="<?=t('Update Set')?>"><i class="fa fa-pencil"></i></a></li>
                    <li><a href="#" data-dialog="delete-set-<?=$set->getId()?>" data-dialog-title="<?=t('Delete Set')?>"><i class="fa fa-trash-o"></i></a></li>
                </ul>

                <div><?=$set->getTitle()?></div>

            </div>

            <table class="table table-hover" style="width: 100%;">
                <tbody>

                <? foreach($set->getControls() as $control) { ?>
                    <? var_dump_safe($control);?>
                <? } ?>

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
        $('[data-dialog]').on('click', function() {
            var $element = $('#ccm-dialog-' + $(this).attr('data-dialog'));
            if ($(this).attr('data-dialog-title')) {
                var title = $(this).attr('data-dialog-title');
            } else {
                var title = $(this).text();
            }
            jQuery.fn.dialog.open({
                element: $element,
                modal: true,
                width: 320,
                title: title,
                height: 'auto'
            });
        });

        $('#ccm-dashboard-content').sortable({
            handle: 'a[data-command=move-set]',
            items: '.ccm-item-set',
            cursor: 'move',
            axis: 'y',
            stop: function() {
                var formData = [{
                    'name': 'token',
                    'value': '<?=Core::make("token")->generate("update_set_display_order")?>'
                }];
                $('.ccm-item-set').each(function() {
                    formData.push({'name': 'set[]', 'value': $(this).attr('data-field-set')});
                });
                $.ajax({
                    type: 'post',
                    data: formData,
                    url: '<?=$view->action("update_set_display_order", $expressForm->getID())?>',
                    success: function() {

                    }
                });
            }
        });

    });
</script>

