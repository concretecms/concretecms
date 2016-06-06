<?php defined('C5_EXECUTE') or die("Access Denied.");?>
<form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('add')?>">
    <?=$token->output('add_entity')?>

    <fieldset>
        <div class="form-group <?php if ($error->containsField('name')) { ?>has-error<?php } ?>">
            <label for="name" class="control-label"><?=t('Name')?></label>
            <?=$form->text('name', '', ['autofocus' => 'autofocus'])?>
            <p class="help-block"><?=t('The name is how your entity will appear in the Dashboard. It may only contain letters.')?></p>
        </div>
        <div class="form-group <?php if ($error->containsField('handle')) { ?>has-error<?php } ?>">
            <label for="name" class="control-label"><?=t('Handle')?></label>
            <?=$form->text('handle')?>
            <p class="help-block"><?=t('A unique string consisting of lowercase letters and underscores only.')?></p>
        </div>
        <div class="form-group">
            <label for="name" class="control-label"><?=t('Description')?></label>
            <?=$form->textarea('description', array('rows' => 5))?>
            <p class="help-block"><?=t('An internal description. This is not publicly displayed.')?></p>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/system/express/entities')?>" class="pull-left btn btn-default" type="button" ><?=t('Back to List')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
</form>