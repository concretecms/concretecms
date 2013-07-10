<?
defined('C5_EXECUTE') or die("Access Denied.");

$ih = Loader::helper('concrete/interface');
$cap = Loader::helper('concrete/dashboard');
$valt = Loader::helper('validation/token');
$valc = Loader::helper('concrete/validation');
$form = Loader::helper('form');
$ctArray = CollectionType::getList();
$args['section'] = 'collection_types';
$u = new User();

Loader::model('file_set');
$pageTypeIconsFS = FileSet::getByName("Page Type Icons");

$cID = Loader::helper('security')->sanitizeInt($_GET['cID']);

if ($cID && $_GET['task'] == 'load_master') { 
	$u->loadMasterCollectionEdit($cID, 1);
	header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID . '&mode=edit');
	exit;
}

if ($_REQUEST['task'] == 'edit') {
	$ct = CollectionType::getByID($_REQUEST['ctID']);
	if (is_object($ct)) { 		
			
		$ctName = $ct->getCollectionTypeName();
		$ctHandle = $ct->getCollectionTypeHandle();		
		$ctName = Loader::helper("text")->entities($ctName);
		$ctHandle = Loader::helper('text')->entities($ctHandle);

		$ctEditMode = true;
	}
}

?>

<?
if ($ctEditMode) { 
	$ct->populateAvailableAttributeKeys();

        $akIDArray = $_POST['akID'];
        if (!is_array($akIDArray)) {
            $akIDArray = array();
        }

	?>
	
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Page Type').'<span class="label" style="position:relative;top:-3px;left:12px;">'.t('* required field').'</span>', false, false, false);?>
    
    <form class="form-horizontal" method="post" id="update_page_type" action="<?=$this->url('/dashboard/pages/types/', 'update')?>">
	<?=$valt->output('update_page_type')?>
    <?=$form->hidden('ctID', $_REQUEST['ctID']); ?>
    <?=$form->hidden('task', 'edit'); ?>
    <?=$form->hidden('update', '1'); ?>
    
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
                        <?=$form->text('ctName', $ctName, array('style' => 'width:100%'))?>
                    </td>
                    <td>
                        <?=$form->text('ctHandle', $ctHandle, array('style' => 'width:100%'))?>
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
                                    if ($ct->getCollectionTypeIcon() == $ic->getFileID() || $first) { 
                                        $checked = 'checked';
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
                                    if ($ct->getCollectionTypeIcon() == $ic || $first) { 
                                        $checked = 'checked';
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
                        <? } ?>
                        </tr>
                    <? } ?>
            </tbody>
        </table>
	</div>

    <? $confirmMsg = t('Are you sure?'); ?>
	<script type="text/javascript">
	deletePageType = function() {
		if(confirm('<?=$confirmMsg?>')){ 
			location.href="<?=$this->url('/dashboard/pages/types/','delete',$_REQUEST['ctID'], $valt->generate('delete_page_type'))?>";
		}	
	}
	</script>
    
    <div class="ccm-pane-footer">
        <? print $ih->submit(t('Save'), 'update_page_type', 'right', 'primary'); ?>
		<? print $ih->button_js(t('Delete'), "deletePageType()", 'right', 'error'); ?>
        <? print $ih->button(t('Cancel'), $this->url('/dashboard/pages/types'), 'left'); ?>
    </div>
    
    </form>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
    
    
<? } else { ?>
    <!-- START: Default Page Types pane -->
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Page Types'), false, false);?>
	
	<div class="clearfix">
       <? print $ih->button(t('Add a Page Type'), $this->url('/dashboard/pages/types/add'), 'right'); ?>
       <br/><br/>
	</div>
	
	<? if (count($ctArray) == 0) { ?>
		<br/><strong><?=t('No page types found.')?></strong><br/><br>
	<? } else { ?>
	
	<table border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-striped">
    	<thead>
            <tr>
                <th width="100%"><?=t('Name')?></th>
                <th><?=t('Handle')?></th>
                <th><?=t('Package')?></th>
                <th <? if ($cap->canAccessComposer()) { ?>colspan="3"<? } else { ?>colspan="2"<? } ?>></th>
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
   
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>
    
    <!-- END: Default Page Type pane -->
	
<? } ?>