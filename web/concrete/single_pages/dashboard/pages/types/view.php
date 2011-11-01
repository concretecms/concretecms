<?
defined('C5_EXECUTE') or die("Access Denied.");

$ih = Loader::helper('concrete/interface');
$cap = Loader::helper('concrete/dashboard');
$valt = Loader::helper('validation/token');
$form = Loader::helper('form');
$ctArray = CollectionType::getList();
$args['section'] = 'collection_types';
$u = new User();

Loader::model('file_set');
$pageTypeIconsFS = FileSet::getByName("Page Type Icons");

if ($_GET['cID'] && $_GET['task'] == 'load_master') { 
	$u->loadMasterCollectionEdit($_GET['cID'], 1);
	header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_GET['cID'] . '&mode=edit');
	exit;
}

if ($_REQUEST['task'] == 'edit') {
	$ct = CollectionType::getByID($_REQUEST['ctID']);
	if (is_object($ct)) { 		
		if ($_POST['update']) {
		
			$ctName = $_POST['ctName'];
			$ctHandle = $_POST['ctHandle'];
			
		} else {
			
			$ctName = $ct->getCollectionTypeName();
			$ctHandle = $ct->getCollectionTypeHandle();
		
		}
		
		$ctEditMode = true;
	}
}

if ($_POST['task'] == 'add' || $_POST['update']) {
	$ctName = $_POST['ctName'];
	$ctHandle = $_POST['ctHandle'];
	
	$error = array();
	if (!$ctHandle) {
		$error[] = t("Handle required.");
	}
	if (!$ctName) {
		$error[] = t("Name required.");
	}
	
	if (!$valt->validate('add_or_update_page_type')) {
		$error[] = $valt->getErrorMessage();
	}
	
	$akIDArray = $_POST['akID'];
	if (!is_array($akIDArray)) {
		$akIDArray = array();
	}
	
	if (count($error) == 0) {
		try {
			if ($_POST['task'] == 'add') {
				$nCT = CollectionType::add($_POST);
				$this->controller->redirect('/dashboard/pages/types?created=1');
			} else if (is_object($ct)) {
				$ct->update($_POST);
				$this->controller->redirect('/dashboard/pages/types?updated=1');
			}		
			exit;
		} catch(Exception $e1) {
			$error[] = $e1->getMessage();
		}
	}
}

if ($_REQUEST['created']) { 
	$message = t('Page Type added.');
} else if ($_REQUEST['updated']) {
	$message = t('Page Type updated.');
}


?>

<?
if ($ctEditMode) { 
	$ct->populateAvailableAttributeKeys();
	?>
	
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Page Type').'<span class="label" style="position:relative;top:-3px;left:12px;">'.t('* required field').'</span>', false, false, false);?>
    
    <form method="post" id="update_page_type" action="<?=$this->url('/dashboard/pages/types/')?>">
	<?=$valt->output('add_or_update_page_type')?>
    <?=$form->hidden('ctID', $_REQUEST['ctID']); ?>
    <?=$form->hidden('task', 'edit'); ?>
    <?=$form->hidden('update', '1'); ?>
    
	<div class="ccm-pane-body">
		
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="header"><?=t('Name')?> <span class="required">*</span></th>
                    <th class="header"><?=t('Handle')?> <span class="required">*</span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?=$form->text('ctName', $ctName, array('class' => 'span9'))?>
                    </td>
                    <td>
                        <?=$form->text('ctHandle', $ctHandle, array('class' => 'span6'))?>
                    </td>
                </tr>
			</tbody>
		</table>
        
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="subheader">
                    <?=t('Icon')?>
                    <?
                        if (!is_object($pageTypeIconsFS)) {
                            print '<span style="margin-left: 4px; color: #aaa">';
                            print t('(To add your own page type icons, create a file set named "%s" and add files to that set)', t('Page Type Icons'));
                            print '</span>';
                        } else {
                            print '<span style="margin-left: 4px; color: #aaa">';
                            print t('(Pulling icons from file set "%s". Icons will be displayed at %s x %s.)', t('Page Type Icons'), COLLECTION_TYPE_ICON_WIDTH, COLLECTION_TYPE_ICON_HEIGHT);
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
                                    if ($ct->getCollectionTypeIcon() == $ic->getFileID() || $first) { 
                                        $checked = 'checked';
                                    }
                                    $first = false;
                                    ?>
                                    <label style="white-space: nowrap; margin: 10px 20px 10px 0; float:left;">
                                    <input type="radio" name="ctIcon" value="<?= $ic->getFileID() ?>" style="vertical-align: middle" <?=$checked?> />
                                    <img src="<?= $fv->getRelativePath(); ?>" width="<?=COLLECTION_TYPE_ICON_WIDTH?>" height="<?=COLLECTION_TYPE_ICON_HEIGHT?>" style="vertical-align: middle" />
                                    </label>
                                <? 
                                } else {
                                    $checked = false;
                                    if ($ct->getCollectionTypeIcon() == $ic || $first) { 
                                        $checked = 'checked';
                                    }
                                    $first = false;
                                    ?>
                                    <label style="white-space: nowrap; margin: 10px 20px 10px 0; float:left;">
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
        
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th colspan="3" class="subheader"><?= t('Default Attributes'); ?></th>
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
                            <label>
                            <input type="checkbox" name="akID[]" value="<?=$ak->getAttributeKeyID()?>" <? if (($this->controller->isPost() && in_array($ak->getAttributeKeyID(), $akIDArray))) { ?> checked <? } else if ((!$this->controller->isPost()) && $ct->isAvailableCollectionTypeAttribute($ak->getAttributeKeyID())) { ?> checked <? } ?> />
                            <span><?=$ak->getAttributeKeyName()?></span>
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
                        <? } ?>
                        </tr>
                    <? } ?>
            </tbody>
        </table>
	</div>
    
    <div class="ccm-pane-footer">
        <? print $ih->submit(t('Update Page Type'), 'update_page_type', 'right', 'primary'); ?>
        <? print $ih->button(t('Cancel'), $this->url('/dashboard/pages/types'), 'left'); ?>
    </div>
    
    </form>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
    
    <!-- END: Edit Page Type pane -->
	
    
    <div class="ccm-spacer" style="height:10px;">&nbsp;</div>
        
        
    <!-- START: Delete Page Type pane -->
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Delete Page Type'), false, false, false);?>
	
    <div class="ccm-pane-body">
		<p><?=t('Click below to remove this page type entirely. (Note: You may only remove page types which are not being used on your site. If a page type is being used, delete all instances of its pages first.)')?></p>
	</div>
	
    <div class="ccm-pane-footer">
		<? print $ih->button_js(t('Delete Page Type'), "deletePageType()", 'left', 'error'); ?>
    </div>
    
    <? $confirmMsg = t('Are you sure?'); ?>
	<script type="text/javascript">
	deletePageType = function() {
		if(confirm('<?=$confirmMsg?>')){ 
			location.href="<?=$this->url('/dashboard/pages/types/','delete',$_REQUEST['ctID'], $valt->generate('delete_page_type'))?>";
		}	
	}
	</script>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
    
    <!-- END: Delete Page Type pane -->
    
<? 
} else if ($_REQUEST['task'] == 'add') {  ?>
	
    <!-- START: Add Page Type pane -->
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Page Type').'<span class="label" style="position:relative;top:-3px;left:12px;">'.t('* required field').'</span>', false, false, false);?>
	
    <form method="post" id="add_page_type" action="<?=$this->url('/dashboard/pages/types/')?>">
	<?=$valt->output('add_or_update_page_type')?>
    <?=$form->hidden('task', 'add'); ?>
	
    <div class="ccm-pane-body">
    
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="header"><?=t('Name')?> <span class="required">*</span></th>
                    <th class="header"><?=t('Handle')?> <span class="required">*</span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?=$form->text('ctName', $_POST['ctName'], array('class' => 'span9'))?>
                    </td>
                    <td>
                        <?=$form->text('ctHandle', $_POST['ctHandle'], array('class' => 'span6'))?>
                    </td>
                </tr>
			</tbody>
		</table>
        
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="subheader">
					
					<?=t('Icon')?>
                    <?
                        if (!is_object($pageTypeIconsFS)) {
                            print '<span style="margin-left: 4px; color: #aaa">';
                            print t('(To add your own page type icons, create a file set named "%s" and add files to that set)', t('Page Type Icons'));
                            print '</span>';
                        } else {
                            print '<span style="margin-left: 4px; color: #aaa">';
                            print t('(Pulling icons from file set "%s". Icons will be displayed at %s x %s.)', t('Page Type Icons'), COLLECTION_TYPE_ICON_WIDTH, COLLECTION_TYPE_ICON_HEIGHT);
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
                            <label style="white-space: nowrap; margin: 10px 20px 10px 0; float:left;">
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
                            <label style="white-space: nowrap; margin: 10px 20px 10px 0; float:left;">
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
        
        <table border="0" cellspacing="0" cellpadding="0">
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
                            <label>
                                <input type="checkbox" name="akID[]" value="<?=$ak->getAttributeKeyID()?>" />
                                <span><?=$ak->getAttributeKeyName()?></span>
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
        <? print $ih->submit(t('Add Page Type'), 'add_page_type', 'right', 'primary'); ?>
        <? print $ih->button(t('Cancel'), $this->url('/dashboard/pages/types'), 'left'); ?>
    </div>
    
    </form>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
    
    <!-- END Add Page Type pane -->

<? } else { ?>
	
    <!-- START: Default Page Types pane -->
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Page Types'), false, false, false);?>
	
    <div class="ccm-pane-body">	

	<? if (count($ctArray) == 0) { ?>
		<br/><strong><?=t('No page types found.')?></strong><br/><br>
	<? } else { ?>
	
	<table border="0" cellspacing="0" cellpadding="0" class="zebra-striped">
    	<thead>
            <tr>
                <th width="100%"><?=t('Name')?></th>
                <th><?=t('Handle')?></th>
                <th><?=t('Package')?></th>
                <th <? if ($cap->canAccessComposer()) { ?>colspan="3"<? } else { ?>colspan="2"<? } ?></th>
            </tr>
		</thead>
		<tbody>
            <? foreach ($ctArray as $ct) { ?>
            <tr>
                <td><?=$ct->getCollectionTypeName()?></td>
                <td><?=$ct->getCollectionTypeHandle()?></td>
                <td><?
                    $package = false;
                    if ($ct->getPackageID() > 0) {
                        $package = Package::getByID($ct->getPackageID());
                    }
                    if (is_object($package)) {
                        print $package->getPackageName(); 
                    } else {
                        print t('None');
                    }
                    ?></td>
                <td>
                <? if ($ct->getMasterCollectionID()) {?>
                    <?
                    $tp = new TaskPermission();
                    if ($tp->canAccessPageDefaults()) { ?>
                        <? print $ih->button(t('Defaults'), $this->url('/dashboard/pages/types?cID=' . $ct->getMasterCollectionID() . '&task=load_master'), 'left','small')?>
                    <? } else { 
                        $defaultsErrMsg = t('You do not have access to page type default content.');
                        ?>
                        <? print $ih->button_js(t('Defaults'), "alert('" . $defaultsErrMsg . "')", 'left', 'small ccm-button-inactive', array('title'=>t('Lets you set default permissions and blocks for a particular page type.')) );?>
                    <? } ?>
                <? } ?>
            
                </td>
                
                <td><? print $ih->button(t('Settings'), $this->url('/dashboard/pages/types?ctID=' . $ct->getCollectionTypeID() . '&task=edit'), 'left','small')?></td>
                <? if ($cap->canAccessComposer()) { ?>
                    <td><? print $ih->button(t('Composer'), $this->url('/dashboard/pages/types/composer', 'view', $ct->getCollectionTypeID()), 'left', 'small')?></td>
                <? } ?>	
            </tr>
            <? } ?>
		</tbody>
	</table>
	
	<? } ?>
    
    </div>
    
    <div class="ccm-pane-footer">
        <? print $ih->button(t('Add a Page Type'), $this->url('/dashboard/pages/types?task=add'), 'left', 'primary'); ?>
    </div>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
    
    <!-- END: Default Page Type pane -->
	
<? } ?>