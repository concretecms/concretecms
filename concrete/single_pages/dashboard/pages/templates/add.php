<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
?>


    <form method="post" id="add_page_template" action="<?=$view->url('/dashboard/pages/templates/add', 'add_page_template')?>">
    <?=$this->controller->token->output('add_page_template')?>
    <?=$form->hidden('task', 'add'); ?>


    <div class="form-group">
        <label for="pTemplateName" class="control-label"><?=t('Name')?></label>
        <?=$form->text('pTemplateName')?>
    </div>

    <div class="form-group">
        <label for="pTemplateHandle" class="control-label"><?=t('Handle')?></label>
        <?=$form->text('pTemplateHandle')?>
    </div>

    <div class="form-group">
        <label for="pTemplateHandle" class="control-label"><?=t('Icon')?></label>
        <div class="row">
        <?php
        $i = 0;
        foreach ($icons as $ic) {
            ?>
          <div class="col-2">
            <label style="">
                 <img src="<?=REL_DIR_FILES_PAGE_TEMPLATE_ICONS.'/'.$ic;
            ?>" class="img-fluid" style="" />
                <div class="text-center">
                 <?=$form->radio('pTemplateIcon', $ic, $i == 0)?>
                    </div>
            </label>
          </div>
          <?php ++$i;
            ?>
        <?php 
        } ?>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <a href="<?=$view->url('/dashboard/pages/templates')?>" class="btn btn-secondary float-left"><?=t("Cancel")?></a>
        <button type="submit" class="btn float-right btn-primary"><?=t('Add')?></button>
    </div>
    </div>
    </form>
