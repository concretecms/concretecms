<?php

defined('C5_EXECUTE') or die("Access Denied.");
$buttonText = t("Add");
/**
 * @var $templates \Concrete\Core\Entity\Board\Template[]
 */
?>

<form method="post" action="<?=$view->action('submit')?>">
    <?=$token->output('submit')?>
    <fieldset>
        <div class="form-group">
            <?=$form->label('boardName', t('Board Name'))?>
            <?=$form->text('boardName')?>
        </div>
        
        <div class="form-group">
            <?=$form->label('boardName', t('Template'))?>
            <?=$form->select('templateID', $templates)?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions ">
                <a href="<?=$view->url('/dashboard/boards/boards')?>" class="btn btn-secondary float-left"><?=t("Cancel")?></a>
                <button type="submit" class="btn btn-primary float-right"><?=$buttonText?></button>
            </div>
        </div>
    </fieldset>
</form>
