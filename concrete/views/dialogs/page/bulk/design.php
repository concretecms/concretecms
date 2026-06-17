<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">
<form method="post" data-dialog-form="design" action="<?=$controller->action('submit')?>">


            <?php foreach ($pages as $c) {
                $cp = new Permissions($c);
                if ($cp->canEditPageTemplate() && $cp->canEditPageType() && $cp->canEditPageTheme()) { ?>
                    <input type="hidden" name="item[]" value="<?=$c->getCollectionID()?>">
            <?php }
            }
            ?>

        <?php if ($containsSinglePages) {
    ?>
            <div class="mb-3">
                <?=$form->label('pTemplateID', t('Page Template'))?>
                <div class="alert alert-info"><?=t('One or more pages selected are a single page. You may not change page templates.')?></div>
            </div>
        <?php
} else {
    ?>
            <div class="mb-3">
                <?=$form->label('pTemplateID', t('Page Template'))?>
                <?=$form->select('pTemplateID', $templatesSelect, $selectedTemplateID)?>
            </div>
        <?php
}
    ?>

    <div class="mb-3">
		<?=$form->label('pTypeID', t('Page Type'))?>
		<?=$form->select('pTypeID', $typesSelect, $selectedTypeID)?>
	</div>

    <div class="mb-3">
		<?=$form->label('pThemeID', t('Theme'))?>
		<?=$form->select('pThemeID', $themesSelect, $selectedThemeID)?>
	</div>


	<div class="dialog-buttons">
	<button class="btn btn-secondary" data-dialog-action="cancel"><?=t('Cancel')?></button>
	<button type="button" data-dialog-action="submit" class="btn btn-primary ms-auto"><?=t('Save')?></button>
	</div>

</form>
</div>
