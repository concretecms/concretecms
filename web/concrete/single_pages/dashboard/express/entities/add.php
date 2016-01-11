<?php defined('C5_EXECUTE') or die("Access Denied.");?>
<form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('add')?>">
    <?=$token->output('add_entity')?>

    <fieldset>
        <div class="form-group">
            <label for="name"><?=t('Name')?></label>
            <?=$form->text('name')?>
        </div>
        <div class="form-group">
            <label for="name"><?=t('Database Table Name')?></label>
            <?=$form->text('table_name')?>
        </div>
        <div class="form-group">
            <label for="name"><?=t('Description')?></label>
            <?=$form->textarea('description', array('rows' => 5))?>
        </div>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/express/entities')?>" class="pull-left btn btn-default" type="button" ><?=t('Back to List')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
</form>