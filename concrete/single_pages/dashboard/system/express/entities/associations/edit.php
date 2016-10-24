<?php defined('C5_EXECUTE') or die("Access Denied.");?>
<form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('save_association', $entity->getID())?>">
    <input type="hidden" name="association_id" value="<?=$association->getID()?>">

    <?=$token->output()?>

    <div class="form-group">
        <label for="name" class="control-label"><?=t('Target Property Name')?></label>
        <?=$form->text('target_property_name', $association->getTargetPropertyName())?>
    </div>

    <div class="form-group">
        <label for="name" class="control-label"><?=t('Inversed Property Name')?></label>
        <?=$form->text('inversed_property_name', $association->getInversedByPropertyName())?>
    </div>


    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/system/express/entities/associations', 'view_association_details', $association->getID())?>" class="pull-left btn btn-default" type="button" ><?=t('Back to Association')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
</form>