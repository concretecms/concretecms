<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<? if (is_object($template) && ($this->controller->getTask() == 'edit' || $this->controller->getTask() == 'update')) {
    $form = Loader::helper('form');
?>
  
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Update Page Template'), false, false, false);?>
    
    <form method="post" class="form-horizontal" id="update_page_template" action="<?=$view->url('/dashboard/pages/templates', 'update')?>">
    <?=$this->controller->token->output('update_page_template')?>
    <input type="hidden" name="pTemplateID" value="<?=$template->getPageTemplateID()?>" />
    <div class="ccm-pane-body">

        <? $confirmMsg = t('Are you sure?'); ?>
        <script type="text/javascript">
        deleteTemplate = function() {
            if(confirm('<?=$confirmMsg?>')){ 
                location.href="<?=$view->url('/dashboard/pages/templates/','delete',$template->getPageTemplateID(), $this->controller->token->generate('delete_page_template'))?>";
            }   
        }
        </script>


        
        <div class="form-group">
            <label for="pTemplateName" class="col-md-2 control-label"><?=t('Name')?></label>
            <div class="col-md-10">
                <?=$form->text('pTemplateName', $template->getPageTemplateName())?>
            </div>
        </div>

        <div class="form-group">
            <label for="pTemplateHandle" class="col-md-2 control-label"><?=t('Handle')?></label>
            <div class="col-md-10">
                <?=$form->text('pTemplateHandle', $template->getPageTemplateHandle())?>
            </div>
        </div>

        <div class="form-group">
            <label for="pTemplateHandle" class="col-md-2 control-label"><?=t('Icon')?></label>
            <div class="col-md-10">

            <?
            $i = 0;
            foreach($icons as $ic) { ?>
              <div class="col-sm-2">
                <label style="text-align: center">
                     <img src="<?=REL_DIR_FILES_PAGE_TEMPLATE_ICONS.'/'.$ic;?>" width="<?=PAGE_TEMPLATE_ICON_WIDTH?>" height="<?=PAGE_TEMPLATE_ICON_HEIGHT?>" style="vertical-align: middle" />
                     <?=$form->radio('pTemplateIcon', $ic, $ic == $template->getPageTemplateIcon())?>
                </label>
              </div>
              <? $i++; ?>
            <? } ?>
            </div>
        </div>
  
    </div>
    
    <div class="ccm-pane-footer">
        <a href="<?=$view->url('/dashboard/pages/templates')?>" class="btn btn-default pull-left"><?=t("Cancel")?></a>
        <div class="btn-toolbar pull-right">
            <button class="btn btn-danger" onclick="deleteTemplate()" type="button"><?=t('Delete Template')?></button>
            <button type="submit" class="btn btn-primary"><?=t('Update')?></button>
        </div>
    </div>
    
    </form>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

<? } else { ?>
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Page Types'), false, false);?>

    <? if (count($templates) == 0) { ?>
        <br/><strong><?=t('No page types found.')?></strong><br/><br>
    <? } else { ?>

    <div class="row">

        <? foreach($templates as $pt) { ?>
          <div class="col-md-2">
            <div class="thumbnail" style="text-align: center">
                <div style="text-align: center"><?=$pt->getPageTemplateIconImage()?></div>
                <div class="caption">
                <h4><?=$pt->getPageTemplateName()?></h4>
                <p><a href="<?=$view->action('edit', $pt->getPageTemplateID())?>" class="btn btn-default"><?=t('Edit')?></a></p>
                </div>
            </div>
          </div>

        <? } ?>

    </div>

    <? } ?>
    <br/>
    <div class="clearfix"><a href="<?=$view->url('/dashboard/pages/templates/add')?>" class="btn btn-primary"><?=t('Add Template')?></a></div>

    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>
<? } ?>