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
			
		$ctName = $ct->getCollectionTypeName();
		$ctHandle = $ct->getCollectionTypeHandle();		
		$ctName = Loader::helper("text")->entities($ctName);
		$ctHandle = Loader::helper('text')->entities($ctHandle);

		$ctEditMode = true;
	}
}

if ($_POST['task'] == 'add' || $_POST['update']) {
	$ctName = Loader::helper("text")->entities($_POST['ctName']);
	$ctHandle = Loader::helper('text')->entities($_POST['ctHandle']);
	
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

	<h1><span><?=t('Edit Page Type')?> (<em class="required">*</em> - <?=t('required field')?>)</span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<form method="post" id="update_page_type" action="<?=$this->url('/dashboard/pages/types/')?>">
	<?=$valt->output('add_or_update_page_type')?>
	<input type="hidden" name="ctID" value="<?=$_REQUEST['ctID']?>" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="update" value="1" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" colspan="2"><?=t('Name')?> <span class="required">*</span></td>
		<td class="subheader"><?=t('Handle')?> <span class="required">*</span></td>
	</tr>
	<tr>
		<td style="width: 65%" colspan="2"><input type="text" name="ctName" style="width: 100%" value="<?=$ctName?>" /></td>
		<td style="width: 35%"><input type="text" name="ctHandle" style="width: 100%" value="<?=$ctHandle?>" /></td>
	</tr>
	<tr>
		<td colspan="3" class="subheader"><?=t('Icon')?>
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
		</td>
	</tr>
	<tr>
		<td colspan="3">
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
        	<br style="clear:both"/>
		</td>
	</tr>
	<tr>
		<td colspan="3" class="subheader" class="subheader"><?=t('Default Attributes')?></td>
	</tr>
	<?
		$attribs = CollectionAttributeKey::getList();
		$i = 0;
		foreach($attribs as $ak) { 
		if ($i == 0) { ?>
			<tr>
		<? } ?>
		
		<td><input type="checkbox" name="akID[]" value="<?=$ak->getAttributeKeyID()?>" <? if (($this->controller->isPost() && in_array($ak->getAttributeKeyID(), $akIDArray))) { ?> checked <? } else if ((!$this->controller->isPost()) && $ct->isAvailableCollectionTypeAttribute($ak->getAttributeKeyID())) { ?> checked <? } ?> /> <?=$ak->getAttributeKeyName()?></td>
		
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
	<tr>
		<td colspan="3" class="header">
		<? print $ih->submit(t('Update Page Type'), 'update_page_type', 'right');?>
		<? print $ih->button(t('Cancel'), $this->url('/dashboard/pages/types'), 'left');?>
		</td>
	</tr>
    </table>
	</div>
	
	<br>
	</form>	
	</div>
	
	<h1><span><?=t('Delete Page Type')?></span></h1>
	<div class="ccm-dashboard-inner">


	<p><?=t('Click below to remove this page type entirely. (Note: You may only remove page types which are not being used on your site. If a page type is being used, delete all instances of its pages first.)')?> 
	<div class="ccm-spacer">&nbsp;</div>
	
	<? print $ih->button_js(t('Delete Page Type'), "deletePageType()", 'left');?>
	
	<div class="ccm-spacer">&nbsp;</div>
	<?
	$confirmMsg = t('Are you sure?');
	?>
	<script type="text/javascript">
	deletePageType = function() {
		if(confirm('<?=$confirmMsg?>')){ 
			location.href="<?=$this->url('/dashboard/pages/types/','delete',$_REQUEST['ctID'], $valt->generate('delete_page_type'))?>";
		}	
	}
	</script>
	</div>
	
<? 
} else if ($_REQUEST['task'] == 'add') {  ?>
	
	<h1><span><?=t('Add Page Type')?> (<em class="required">*</em> - <?=t('required field')?>)</span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<form method="post" id="add_page_type" action="<?=$this->url('/dashboard/pages/types/')?>">
	<?=$valt->output('add_or_update_page_type')?>
	<input type="hidden" name="task" value="add" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" colspan="2"><?=t('Name')?> <span class="required">*</span></td>
		<td class="subheader"><?=t('Handle')?> <span class="required">*</span></td>
	</tr>	
	<tr>
		<td style="width: 65%"  colspan="2"><input type="text" name="ctName" style="width: 100%" value="<?=$ctName?>" /></td>
		<td style="width: 35%"><input type="text" name="ctHandle" style="width: 100%" value="<?=$ctHandle?>" /></td>
	</tr>
	<tr>
		<td colspan="3" class="subheader"><?=t('Icon')?>
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

		</td>
	</tr>
	<tr>
		<td colspan="3">
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
		<br style="clear:both"/>
        </td>
	</tr>
	<tr>
		<td colspan="3" class="subheader"><?=t('Default Attributes to Display')?></td>
	</tr>
	<?
		$attribs = CollectionAttributeKey::getList();
		$i = 0;
		foreach($attribs as $ak) { 
		if ($i == 0) { ?>
			<tr>
		<? } ?>
		
		<td><input type="checkbox" name="akID[]" value="<?=$ak->getAttributeKeyID()?>" /> <?=$ak->getAttributeKeyName()?></td>
		
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
	 <tr>
	<td colspan="3" class="header">
	   <? print $ih->submit(t('Add Page Type'), 'add_page_type', 'right');?>
	   <? print $ih->button(t('Cancel'), $this->url('/dashboard/pages/types'), 'left');?>
	   </td>
	</tr>
	</table>

	</div>
	
	<br>
	</form>	
	</div>

<?
} else { ?>

	<h1><span><?=t('Page Types')?></span></h1>
	<div class="ccm-dashboard-inner">
	

	<? if (count($ctArray) == 0) { ?>
		<br/><strong><?=t('No page types found.')?></strong><br/><br>
	<? } else { ?>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list" width="600">
	<tr>
		<td class="subheader" width="100%"><?=t('Name')?></td>
		<td class="subheader"><?=t('Handle')?></td>
		<td class="subheader"><?=t('Package')?></td>
		<td class="subheader"><div style="width: 90px"></div></td>
		<td class="subheader"><div style="width: 60px"></div></td>
		<? if ($cap->canAccessComposer()) { ?>
			<td class="subheader"><div style="width: 60px"></div></td>
		<? } ?>
	</tr>
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
				<? print $ih->button(t('Defaults'), $this->url('/dashboard/pages/types?cID=' . $ct->getMasterCollectionID() . '&task=load_master'))?>
			<? } else { 
				$defaultsErrMsg = t('You do not have access to page type default content.');
				?>
				<? print $ih->button_js(t('Defaults'), "alert('" . $defaultsErrMsg . "')", 'left', 'ccm-button-inactive', array('title'=>t('Lets you set default permissions and blocks for a particular page type.')) );?>
			<? } ?>
		<? } ?>
	
		</td>
		
		<td><? print $ih->button(t('Settings'), $this->url('/dashboard/pages/types?ctID=' . $ct->getCollectionTypeID() . '&task=edit'))?></td>
		<? if ($cap->canAccessComposer()) { ?>
			<td><? print $ih->button(t('Composer'), $this->url('/dashboard/pages/types/composer', 'view', $ct->getCollectionTypeID()))?></td>
		<? } ?>	
	</tr>
	<? } ?>
	
	</table>
	</div>
	
	<? } ?>
	
	<br/>
	<div class="ccm-buttons">
		<a class="ccm-button" href="<?=$this->url('/dashboard/pages/types?task=add')?>"><span><em class="ccm-button-add"><?=t('Add a Page Type')?></em></span></a>	
	</div>
	<div class="ccm-spacer">&nbsp;</div>

	</div>
	
	

	
	
<? } ?>