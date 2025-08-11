<?php defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-dashboard-header-buttons">
<form method="post" action="<?=$view->action('update_permissions_inheritance')?>">
    <input type="hidden" name="boardID" value="<?=$board->getBoardID()?>">
<?=$token->output('update_permissions_inheritance')?>
            <?php if (!$board->arePermissionsSetToOverride()) {
    ?>
                <button name="update_inheritance" value="override" class="btn btn-secondary float-end"><?=t('Override Default Permissions')?></button>
            <?php 
} else {
    ?>
                <button name="update_inheritance" value="revert" class="btn btn-secondary float-end"><?=t('Remove Custom Permissions')?></button>
            <?php 
} ?>
</form>
</div>

<form method="post" enctype="multipart/form-data" action="<?=$view->action('save_permissions')?>">
    <input type="hidden" name="boardID" value="<?=$board->getBoardID()?>">
    <?=$token->output('save_permissions')?>
    <fieldset>
        <div id="ccm-permission-list-form">
            <?php View::element('permission/lists/board', array(
                "editPermissions" => $board->arePermissionsSetToOverride(), "board" => $board, )); ?>
        </div>

    </fieldset>


    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">

    <?php if ($board->arePermissionsSetToOverride()) {
    ?>
        <button type="submit" name="submit" class="btn btn-primary float-end"><?=t('Save')?></button>
    <?php 
} ?>

        </div>
    </div>
</form>
