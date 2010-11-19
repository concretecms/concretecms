<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$ih = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');

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

<?php 
if ($ctEditMode) { 
	$ct->populateAvailableAttributeKeys();
	?>	

	<h1><span><?php echo t('Edit Page Type')?> (<em class="required">*</em> - <?php echo t('required field')?>)</span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<form method="post" id="update_page_type" action="<?php echo $this->url('/dashboard/pages/types/')?>">
	<?php echo $valt->output('add_or_update_page_type')?>
	<input type="hidden" name="ctID" value="<?php echo $_REQUEST['ctID']?>" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="update" value="1" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" colspan="2"><?php echo t('Name')?> <span class="required">*</span></td>
		<td class="subheader"><?php echo t('Handle')?> <span class="required">*</span></td>
	</tr>
	<tr>
		<td style="width: 65%" colspan="2"><input type="text" name="ctName" style="width: 100%" value="<?php echo $ctName?>" /></td>
		<td style="width: 35%"><input type="text" name="ctHandle" style="width: 100%" value="<?php echo $ctHandle?>" /></td>
	</tr>
	<tr>
		<td colspan="3" class="subheader"><?php echo t('Icon')?>
		<?php 
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
		<?php  
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
				<input type="radio" name="ctIcon" value="<?php echo  $ic->getFileID() ?>" style="vertical-align: middle" <?php echo $checked?> />
				<img src="<?php echo  $fv->getRelativePath(); ?>" width="<?php echo COLLECTION_TYPE_ICON_WIDTH?>" height="<?php echo COLLECTION_TYPE_ICON_HEIGHT?>" style="vertical-align: middle" />
				</label>
			<?php  
			} else {
            	$checked = false;
				if ($ct->getCollectionTypeIcon() == $ic || $first) { 
					$checked = 'checked';
				}
				$first = false;
				?>
				<label style="white-space: nowrap; margin: 10px 20px 10px 0; float:left;">
				<input type="radio" name="ctIcon" value="<?php echo  $ic ?>" style="vertical-align: middle" <?php echo $checked?> />
					<img src="<?php echo REL_DIR_FILES_COLLECTION_TYPE_ICONS.'/'.$ic;?>" width="<?php echo COLLECTION_TYPE_ICON_WIDTH?>" height="<?php echo COLLECTION_TYPE_ICON_HEIGHT?>" style="vertical-align: middle" />
                </label>
            <?php 
			}
		
		} ?>
        	<br style="clear:both"/>
		</td>
	</tr>
	<tr>
		<td colspan="3" class="header" class="subheader"><?php echo t('Default Attributes')?></td>
	</tr>
	<?php 
		$attribs = CollectionAttributeKey::getList();
		$i = 0;
		foreach($attribs as $ak) { 
		if ($i == 0) { ?>
			<tr>
		<?php  } ?>
		
		<td><input type="checkbox" name="akID[]" value="<?php echo $ak->getAttributeKeyID()?>" <?php  if (($this->controller->isPost() && in_array($ak->getAttributeKeyID(), $akIDArray))) { ?> checked <?php  } else if ((!$this->controller->isPost()) && $ct->isAvailableCollectionTypeAttribute($ak->getAttributeKeyID())) { ?> checked <?php  } ?> /> <?php echo $ak->getAttributeKeyName()?></td>
		
		<?php  $i++;
		
		if ($i == 3) { ?>
		</tr>
		<?php  
		$i = 0;
		}
		
	}
	
	if ($i < 3 && $i > 0) {
		for ($j = $i; $j < 3; $j++) { ?>
			<td>&nbsp;</td>
		<?php  }
	?></tr>
	<?php  } ?>
	<tr>
		<td colspan="3" class="header">
		<?php  print $ih->submit(t('Update Page Type'), 'update_page_type', 'right');?>
		<?php  print $ih->button(t('Cancel'), $this->url('/dashboard/pages/types'), 'left');?>
		</td>
	</tr>
    </table>
	</div>
	
	<br>
	</form>	
	</div>
	
	<h1><span><?php echo t('Delete Page Type')?></span></h1>
	<div class="ccm-dashboard-inner">


	<p><?php echo t('Click below to remove this page type entirely. (Note: You may only remove page types which are not being used on your site. If a page type is being used, delete all instances of its pages first.)')?> 
	<div class="ccm-spacer">&nbsp;</div>
	
	<?php  print $ih->button_js(t('Delete Page Type'), "deletePageType()", 'left');?>
	
	<div class="ccm-spacer">&nbsp;</div>
	<?php 
	$confirmMsg = t('Are you sure?');
	?>
	<script type="text/javascript">
	deletePageType = function() {
		if(confirm('<?php echo $confirmMsg?>')){ 
			location.href="<?php echo $this->url('/dashboard/pages/types/','delete',$_REQUEST['ctID'], $valt->generate('delete_page_type'))?>";
		}	
	}
	</script>
	</div>
	
<?php  
} else if ($_REQUEST['task'] == 'add') {  ?>
	
	<h1><span><?php echo t('Add Page Type')?> (<em class="required">*</em> - <?php echo t('required field')?>)</span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<form method="post" id="add_page_type" action="<?php echo $this->url('/dashboard/pages/types/')?>">
	<?php echo $valt->output('add_or_update_page_type')?>
	<input type="hidden" name="task" value="add" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" colspan="2"><?php echo t('Name')?> <span class="required">*</span></td>
		<td class="subheader"><?php echo t('Handle')?> <span class="required">*</span></td>
	</tr>	
	<tr>
		<td style="width: 65%"  colspan="2"><input type="text" name="ctName" style="width: 100%" value="<?php echo $_POST['ctName']?>" /></td>
		<td style="width: 35%"><input type="text" name="ctHandle" style="width: 100%" value="<?php echo $_POST['ctHandle']?>" /></td>
	</tr>
	<tr>
		<td colspan="3" class="subheader"><?php echo t('Icon')?>
		<?php 
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
		<?php  
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
				<input type="radio" name="ctIcon" value="<?php echo  $ic->getFileID() ?>" style="vertical-align: middle" <?php echo $checked?> />
				<img src="<?php echo  $fv->getRelativePath(); ?>" width="<?php echo COLLECTION_TYPE_ICON_WIDTH?>" height="<?php echo COLLECTION_TYPE_ICON_HEIGHT?>" style="vertical-align: middle" />
				</label>
			<?php  
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
				<input type="radio" name="ctIcon" value="<?php echo  $ic ?>" style="vertical-align: middle" <?php echo $checked?> />
					<img src="<?php echo REL_DIR_FILES_COLLECTION_TYPE_ICONS.'/'.$ic;?>" width="<?php echo COLLECTION_TYPE_ICON_WIDTH?>" height="<?php echo COLLECTION_TYPE_ICON_HEIGHT?>" style="vertical-align: middle" />
                </label>
            <?php 
			}
		
		} ?>
		<br style="clear:both"/>
        </td>
	</tr>
	<tr>
		<td colspan="3" class="subheader"><?php echo t('Default Attributes')?></td>
	</tr>
	<?php 
		$attribs = CollectionAttributeKey::getList();
		$i = 0;
		foreach($attribs as $ak) { 
		if ($i == 0) { ?>
			<tr>
		<?php  } ?>
		
		<td><input type="checkbox" name="akID[]" value="<?php echo $ak->getAttributeKeyID()?>" /> <?php echo $ak->getAttributeKeyName()?></td>
		
		<?php  $i++;
		
		if ($i == 3) { ?>
		</tr>
		<?php  
		$i = 0;
		}
		
	}
	
	if ($i < 3 && $i > 0) {
		for ($j = $i; $j < 3; $j++) { ?>
			<td>&nbsp;</td>
		<?php  }
	?></tr>
	<?php  } ?>
	<tr>
		<td colspan="3" class="header">
		<?php  print $ih->submit(t('Add Page Type'), 'add_page_type', 'right');?>
		<?php  print $ih->button(t('Cancel'), $this->url('/dashboard/pages/types'), 'left');?>
		</td>
	</tr>
	</table>
	</div>
	
	<br>
	</form>	
	</div>

<?php 
} else { ?>

	<h1><span><?php echo t('Page Types')?></span></h1>
	<div class="ccm-dashboard-inner">
	

	<?php  if (count($ctArray) == 0) { ?>
		<br/><strong><?php echo t('No page types found.')?></strong><br/><br>
	<?php  } else { ?>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list" width="600">
	<tr>
		<td class="subheader" width="100%"><?php echo t('Name')?></td>
		<td class="subheader"><?php echo t('Handle')?></td>
		<td class="subheader"><?php echo t('Package')?></td>
		<td class="subheader"><div style="width: 90px"></div></td>
		<td class="subheader"><div style="width: 60px"></div></td>
	</tr>
	<?php  foreach ($ctArray as $ct) { ?>
	<tr>
		<td><?php echo $ct->getCollectionTypeName()?></td>
		<td><?php echo $ct->getCollectionTypeHandle()?></td>
		<td><?php 
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
		<?php  if ($ct->getMasterCollectionID()) {?>
			<?php 
			$tp = new TaskPermission();
			if ($tp->canAccessPageDefaults()) { ?>
				<?php  print $ih->button_js(t('Defaults'), "window.open('" . $this->url('/dashboard/pages/types?cID=' . $ct->getMasterCollectionID() . '&task=load_master')."')", 'left', false, array('title'=>t('Lets you set default permissions and blocks for a particular page type.')) );?>
			<?php  } else { 
				$defaultsErrMsg = t('You do not have access to page type default content.');
				?>
				<?php  print $ih->button_js(t('Defaults'), "alert('" . $defaultsErrMsg . "')", 'left', 'ccm-button-inactive', array('title'=>t('Lets you set default permissions and blocks for a particular page type.')) );?>
			<?php  } ?>
		<?php  } ?>
	
		</td>
		<td><?php  print $ih->button(t('Edit'), $this->url('/dashboard/pages/types?ctID=' . $ct->getCollectionTypeID() . '&task=edit'))?></td>

	</tr>
	<?php  } ?>
	
	</table>
	</div>
	
	<?php  } ?>
	
	<br/>
	<div class="ccm-buttons">
		<a class="ccm-button" href="<?php echo $this->url('/dashboard/pages/types?task=add')?>"><span><em class="ccm-button-add"><?php echo t('Add a Page Type')?></em></span></a>	
	</div>
	<div class="ccm-spacer">&nbsp;</div>

	</div>
	
	

	
	
<script type="text/javascript">
$(function() {
	$("#ccm-toggle-pages").click(function() {
		if (this.checked) {
			$("tr.ccm-core-package-row").css('display', 'none');
		} else {
			$("tr.ccm-core-package-row").css('display', 'table-row');
		}
	});
});
</script>

<?php  }