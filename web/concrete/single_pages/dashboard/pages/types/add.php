<?
defined('C5_EXECUTE') or die("Access Denied.");

$ih = Loader::helper('concrete/interface');
$cap = Loader::helper('concrete/dashboard');
$valt = Loader::helper('validation/token');
$form = Loader::helper('form');
$u = new User();

Loader::model('file_set');
$pageTypeIconsFS = FileSet::getByName("Page Type Icons");

?>
	
    <!-- START: Add Page Type pane -->
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Page Type'), false, false, false);?>
	
    <form method="post" class="form-horizontal" id="add_page_type" action="<?=$this->url('/dashboard/pages/types/add', 'do_add')?>">
	<?=$valt->output('add_page_type')?>
    <?=$form->hidden('task', 'add'); ?>
	
    <div class="ccm-pane-body">
    
        <table class="table" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="header"><?=t('Name')?> <span class="required">*</span></th>
                    <th class="header"><?=t('Handle')?> <span class="required">*</span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 60%">
                        <?=$form->text('ctName', $_POST['ctName'], array('style' => 'width: 100%'))?>
                    </td>
                    <td>
                        <?=$form->text('ctHandle', $_POST['ctHandle'], array('style' => 'width: 100%'))?>
                    </td>
                </tr>
			</tbody>
		</table>
        
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
        
        <table class="table" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th colspan="3" class="subheader"><?=t('Default Attributes to Display')?></th>
                </tr>
			</thead>
            <tbody>
                <?
                    $attribs = CollectionAttributeKey::getList();
                    $i = 0;
                    foreach($attribs as $ak) { 
                    if ($i == 0) { ?>
                        <tr class="inputs-list">
                    <? } ?>
                    
                        <td width="33%">
                            <label class="">
                                <input type="checkbox" name="akID[]" value="<?=$ak->getAttributeKeyID()?>" <?= (isset($_POST['akID']) && is_array($_POST['akID']) && in_array($ak->getAttributeKeyID(), $_POST['akID'])) ? 'checked' : ''; ?> />
                                <span><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></span>
                            </label>
                        </td>
                    
                    <? $i++;
                    
                    if ($i == 3) { ?>
                    </tr>
                    <? 
                    $i = 0;
                    }
                    
                }
                
                if ($i < 3 && $i > 0) {
                    for ($j = $i; $j < 3; $j++) { ?>
                        <td>&nbsp;</td>
                    <? }
                ?></tr>
        	<? } ?>
        	</tbody>
        </table>
	
	</div>
    
    <div class="ccm-pane-footer">
        <? print $ih->submit(t('Add'), 'add_page_type', 'right', 'primary'); ?>
        <? print $ih->button(t('Cancel'), $this->url('/dashboard/pages/types'), 'left'); ?>
    </div>
    
    </form>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
    
    <!-- END Add Page Type pane -->