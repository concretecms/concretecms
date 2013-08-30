<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
?>
  
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Page Type'), false, false, false);?>
	
    <form method="post" class="form-horizontal" id="add_page_template" action="<?=$this->url('/dashboard/pages/templates/add', 'add_page_template')?>">
	<?=$this->controller->token->output('add_page_template')?>
    <?=$form->hidden('task', 'add'); ?>
	
    <div class="ccm-pane-body">
        
        <div class="form-group">
            <label for="pTemplateName" class="col-lg-2 control-label"><?=t('Name')?></label>
            <div class="col-lg-10">
                <?=$form->text('pTemplateName')?>
            </div>
        </div>

        <div class="form-group">
            <label for="pTemplateHandle" class="col-lg-2 control-label"><?=t('Handle')?></label>
            <div class="col-lg-10">
                <?=$form->text('pTemplateHandle')?>
            </div>
        </div>

        <? /*

        <table class="table" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="subheader">
					
					<?=t('Icon')?>
                    <?
                        if (!is_object($pageTypeIconsFS)) {
                            print '<span style="margin-left: 4px; color: #aaa">';
                            print t('(To add your own page type icons, create a file set named "%s" and add files to that set)', 'Page Type Icons');
                            print '</span>';
                        } else {
                            print '<span style="margin-left: 4px; color: #aaa">';
                            print t('(Pulling icons from file set "%s". Icons will be displayed at %s x %s.)', 'Page Type Icons', COLLECTION_TYPE_ICON_WIDTH, COLLECTION_TYPE_ICON_HEIGHT);
                            print '</span>';
                        }
                    ?>
            
                    </th>
                </tr>
			</thead>
            <tbody>
                <tr>
                    <td>
                    <? 
                    $first = true;
                    foreach($icons as $ic) { 
                        if(is_object($ic)) {
                            $fv = $ic->getApprovedVersion(); 
                            $checked = false;
                            if (isset($_POST['ctIcon']) && $_POST['ctIcon'] == $ic->getFileID()) {
                                $checked = 'checked';
                            } else {
                                if ($first) { 
                                    $checked = 'checked';
                                }
                            }
                            $first = false;
                            ?>
                            <label class="checkbox inline">
                            <input type="radio" name="ctIcon" value="<?= $ic->getFileID() ?>" style="vertical-align: middle" <?=$checked?> />
                            <img src="<?= $fv->getRelativePath(); ?>" width="<?=COLLECTION_TYPE_ICON_WIDTH?>" height="<?=COLLECTION_TYPE_ICON_HEIGHT?>" style="vertical-align: middle" />
                            </label>
                        <? 
                        } else {
                            $checked = false;
                            if (isset($_POST['ctIcon']) && $_POST['ctIcon'] == $ic) {
                                $checked = 'checked';
                            } else {
                                if ($first) { 
                                    $checked = 'checked';
                                }
                            }
                            $first = false;
                            ?>
                            <label class="checkbox inline">
                            <input type="radio" name="ctIcon" value="<?= $ic ?>" style="vertical-align: middle" <?=$checked?> />
                                <img src="<?=REL_DIR_FILES_COLLECTION_TYPE_ICONS.'/'.$ic;?>" width="<?=COLLECTION_TYPE_ICON_WIDTH?>" height="<?=COLLECTION_TYPE_ICON_HEIGHT?>" style="vertical-align: middle" />
                            </label>
                        <?
                        }
                    
                    } ?>
                    </td>
                </tr>
			</tbody>
		</table>
        */ ?>


	
	</div>
    
    <div class="ccm-pane-footer">
        <a href="<?=$this->url('/dashboard/pages/templates')?>" class="btn pull-left"><?=t("Cancel")?></a>
        <button type="submit" class="btn pull-right btn-primary"><?=t('Add')?></button>
    </div>
    
    </form>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>