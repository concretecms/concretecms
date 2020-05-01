<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<?php

$type_menu->render();

?>

<?php if ($controller->getTask() == 'add_group'
    || $controller->getTask() == 'create_group'
    || $controller->getTask() == 'edit_group'
    || $controller->getTask() == 'update_group'
    || $controller->getTask() == 'delete_group') {
    $url = '';
    $ssHandle = '';
    $action = $view->action('create_group', $type->getSiteTypeID());
    $tokenString = 'create_group';
    $buttonText = t('Add');
    if (is_object($group)) {
        $groupName = $group->getSiteGroupName();
        $action = $view->action('update_group', $group->getSiteGroupID());
        $tokenString = 'update_group';
        $buttonText = t('Save');
    }
    ?>

    <?php if (is_object($group)) {
        ?>
        <div class="ccm-dashboard-header-buttons">
            <button data-dialog="delete-group" class="btn btn-danger"><?php echo t("Delete Group")?></button>
        </div>

        <div style="display: none">
            <div id="ccm-dialog-delete-site-group" class="ccm-ui">
                <form method="post" class="form-stacked" action="<?=$view->action('delete_group')?>">
                    <?=Loader::helper("validation/token")->output('delete_group')?>
                    <input type="hidden" name="siteGID" value="<?=$group->getSiteGroupID()?>" />
                    <p><?=t('Are you sure? This action cannot be undone.')?></p>
                </form>
                <div class="dialog-buttons">
                    <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                    <button class="btn btn-danger pull-right" onclick="$('#ccm-dialog-delete-site-group form').submit()"><?=t('Delete Group')?></button>
                </div>
            </div>
        </div>

        <?php
    }
    ?>

    <script type="text/javascript">
        $(function() {
            $('button[data-dialog=delete-group]').on('click', function() {
                jQuery.fn.dialog.open({
                    element: '#ccm-dialog-delete-site-group',
                    modal: true,
                    width: 320,
                    title: '<?=t("Delete Site Group")?>',
                    height: 'auto'
                });
            });
        });
    </script>

    <form method="post" action="<?=$action?>">
        <?=$this->controller->token->output($tokenString)?>

        <div class="form-group">
            <?=$form->label('groupName', t('Name'))?>
            <?=$form->text('groupName', $groupName)?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?=URL::to('/dashboard/system/multsite/types', 'view_groups', $type->getSiteTypeID())?>" class="btn btn-default pull-left"><?=t("Cancel")?></a>
                <button class="pull-right btn btn-success" type="submit" ><?=$buttonText?></button>
            </div>
        </div>

    </form>
    <?php
} else {
    ?>


    <div class="ccm-dashboard-header-buttons">
        <a href="<?php echo View::url('/dashboard/system/multisite/types', 'add_group', $type->getSiteTypeID())?>" class="btn btn-primary"><?php echo t("Add Group")?></a>
    </div>


    <?php if (count($groups) > 0) {
        ?>

        <ul class="item-select-list">
            <?php foreach ($groups as $group) {
                ?>
                <li><a href="<?=$this->action('edit_group', $group->getSiteGroupID())?>"><i class="fa fa-users"></i> <?=$group->getSiteGroupName()?></a></li>
                <?php
            }
            ?>
        </ul>


        <?php
    } else {
        ?>
        <p><?=t("You have not added any site groups.")?></p>
        <?php
    }
    ?>


    <?php
} ?>