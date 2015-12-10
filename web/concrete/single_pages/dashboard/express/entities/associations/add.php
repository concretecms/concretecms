<? defined('C5_EXECUTE') or die("Access Denied.");?>
<form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('add', $entity->getID())?>">
    <?=$token->output('add_association')?>

    <fieldset>
        <div class="form-group">
            <label for="name"><?=t('Source Object')?></label>
            <p><?=$entity->getName()?></p>
        </div>
        <div class="form-group">
            <label for="name"><?=t('Type')?></label>
            <?=$form->select('type', $types)?>
        </div>
        <div class="form-group">
            <label for="name"><?=t('Target Object')?></label>
            <?=$form->select('target_entity', $entities)?>
        </div>
        <div class="form-group">
            <label for="name"><?=t('Target Property Name')?></label>
            <?=$form->text('target_property_name')?>
        </div>
        <div class="form-group">
            <label for="name"><?=t('Inversed Property Name')?></label>
            <?=$form->text('inversed_property_name')?>
        </div>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/express/entities/associations', $entity->getId())?>"
               class="pull-left btn btn-default" type="button" ><?=t('Back to Associations')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
</form>