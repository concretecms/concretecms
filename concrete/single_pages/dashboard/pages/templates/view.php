<?php
defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php
$form = app('helper/form');
?>


<?php if (isset($template) && is_object($template) && ($this->controller->getTask() == 'edit' || $this->controller->getTask() == 'update')) {
    ?>
    <form method="post" class="form-horizontal" id="update_page_template" action="<?=$view->url('/dashboard/pages/templates', 'update'); ?>">
    <?=$this->controller->token->output('update_page_template'); ?>
    <input type="hidden" name="pTemplateID" value="<?=$template->getPageTemplateID(); ?>" />


        
        <div class="form-group row">
            <label for="pTemplateName" class="col-2 "><?=t('Name'); ?></label>
            <div class="col-10">
                <?=$form->text('pTemplateName', $template->getPageTemplateName()); ?>
           </div>
        </div>

        <div class="form-group row">
            <label for="pTemplateHandle" class="col-2"><?=t('Handle'); ?></label>
            <div class="col-10">
                <?=$form->text('pTemplateHandle', $template->getPageTemplateHandle()); ?>
            </div>
        </div>

        <div class="form-group row">
            <label for="pTemplateHandle" class="col-2"><?=t('Icon'); ?></label>
            <div class="col-10">
            <div class="row">
            <?php
            $templateIcon = $template->getPageTemplateIcon();
    $i = 0;
    foreach ($icons as $ic) {
        ?>
              <div class="col-2 text-center">
                <label>
                     <img src="<?=REL_DIR_FILES_PAGE_TEMPLATE_ICONS . '/' . $ic; ?>" class="img-fluid" />
                     <div class="form-check">
                            <?=$form->radio('pTemplateIcon', $ic, $ic == $templateIcon); ?>
                     </div>
                    <label class="form-check-label" > </label>
                </label>
              </div>
              <?php $i++; ?>
            <?php
    } ?>   </div>
            </div>
        </div>
      
    <div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <a href="<?=$view->url('/dashboard/pages/templates'); ?>" class="btn btn-secondary float-left"><?=t('Cancel'); ?></a>
        <div class="btn-toolbar float-right">
            <button class="btn btn-danger mr-1"   data-toggle="modal" data-target="#delete-template" type="button"><?=t('Delete Template'); ?></button>
            <button type="submit" class="btn btn-primary"><?=t('Update'); ?></button>
        </div>
    </div>
    </div> 

    </form>
    

<?php
} else {
        ?>

    <div class="ccm-dashboard-header-buttons">
        <a href="<?php echo View::url('/dashboard/pages/templates/add'); ?>" class="btn btn-primary"><?php echo t('Add Template'); ?></a>
    </div>

    <?php if (count($templates) == 0) {
            ?>
        <br/><strong><?=t('No page types found.'); ?></strong><br/><br>
    <?php
        } else {
            ?>

        <table class="table table-striped">

    <?php foreach ($templates as $pt) {
                ?>
        <tr>
            <td><a href="<?=$view->action('edit', $pt->getPageTemplateID()); ?>"><?=$pt->getPageTemplateIconImage(); ?></a></td>
            <td style="width: 100%; vertical-align: middle"><a href="<?=$view->action('edit', $pt->getPageTemplateID()); ?>"><p class="lead" style="margin-bottom: 0px"><?=$pt->getPageTemplateDisplayName(); ?></p></a></td>
        </tr>
    <?php
            } ?>


        </table>

    <?php
        } ?>

<?php
    } ?>


<?php if ($template) {
        ?>

    <div class="modal fade" id="delete-template" tabindex="-1">
        <form method="post" action="<?=$view->url('/dashboard/pages/templates/', 'delete', $template->getPageTemplateID(), $this->controller->token->generate('delete_page_template')); ?>">
            <?=$token->output('delete_template'); ?>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?=t('Delete Template'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <svg><use xlink:href="#icon-dialog-close" /></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?=t('Are you sure?'); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal"><?=t('Cancel'); ?></button>
                        <button type="submit" class="btn btn-danger float-right"><?=t('Delete Template'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>

<?php
    } ?>
