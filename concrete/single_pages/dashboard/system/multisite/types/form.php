<?php defined('C5_EXECUTE') or die("Access Denied.");

if (isset($type_menu)) {
    $type_menu->render();
}

?>

<form method="post" action="<?=$view->action($action)?>">
    <?php if (is_object($type)) { ?>
        <input type="hidden" name="id" value="<?=$type->getSiteTypeID()?>">
    <?php } ?>
    <?=$token->output('submit')?>
    <div class="form-group">
        <?=$form->label('handle', t('Handle'))?>
        <?=$form->text('handle', $handle)?>
    </div>
    <div class="form-group">
        <?=$form->label('name', t('Name'))?>
        <?=$form->text('name', $name)?>
    </div>
    <div class="form-group">
        <?=$form->label('theme', t('Theme'))?>
        <?=$form->select('theme', $themes, $themeID)?>
    </div>
    <div class="form-group">
        <?=$form->label('template', t('Template for Home Page'))?>
        <?=$form->select('template', $templates, $templateID)?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a class="pull-left btn btn-default" href="<?=$backURL?>"><?=t('Cancel')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=$buttonLabel?></button>
        </div>
    </div>
</form>
