<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<form method="post" action="<?=$view->action('save', $menu->getId())?>">
    <?=$token->output('save')?>

    <div class="mb-3">
        <label class="form-label" for="menuName"><?=t('Name')?></label>
        <?=$form->text('name', $menu->getName())?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/system/basics/menus/details', $menu->getId()); ?>" class="btn btn-secondary float-start"><?=t('Cancel'); ?></a>
            <button type="submit" class="btn-primary btn float-end"><?=t('Save')?></button>
        </div>
    </div>

</form>
