<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$section = 'groups';

if ($_REQUEST['task'] == 'edit') {
	$g = Group::getByID($_REQUEST['gID']);
	if (is_object($g)) { 		
		if ($_POST['update']) {
		
			$gName = $_POST['gName'];
			$gDescription = $_POST['gDescription'];
			
		} else {
			
			$gName = $g->getGroupName();
			$gDescription = $g->getGroupDescription();
		
		}
		
		$editMode = true;
	}
}

$txt = Loader::helper('text');
$ih = Loader::helper('concrete/interface');

if ($_POST['add'] || $_POST['update']) {

	$gName = $txt->sanitize($_POST['gName']);
	$gDescription = $_POST['gDescription'];
	
	$error = array();
	if (!$gName) {
		$error[] = t("Name required.");
	}

	if (count($error) == 0) {
		if ($_POST['add']) {
			$g = Group::add($_POST['gName'], $_POST['gDescription']);
			$this->controller->redirect('/dashboard/groups?created=1');
		} else if (is_object($g)) {
			$g->update($_POST['gName'], $_POST['gDescription']);
			$this->controller->redirect('/dashboard/groups?updated=1');
		}		
		exit;
	}
}

if ($_GET['created']) {
	$message = t("Group Created.");
} else if ($_GET['updated']) {
	$message = t("Group Updated.");
}

if (!$editMode) {

Loader::model('search/group');
$gl = new GroupSearch($_GET);
if ($gl->getTotal() > 0) {
	$gResults = $gl->getResult($_GET['sort'], $_GET['start'], $_GET['order'], 40);
	$pOptions = $gl->paging($_GET['start'], $_GET['order'], 40);
}

?>

<h1><span><?php echo t('Groups')?></span></h1>
<div class="ccm-dashboard-inner">


<form id="ccm-group-search" method="get" style="top: -30px; left: 10px" action="<?php echo $this->url('/dashboard/groups')?>">
<div id="ccm-group-search-fields">
<input type="text" id="ccm-group-search-keywords" name="gKeywords" value="<?php echo $_REQUEST['gKeywords']?>" class="ccm-text" style="width: 100px" />
<input type="submit" value="<?php echo t('Search')?>" />
<input type="hidden" name="group_submit_search" value="1" />
</div>
</form>

<?php  if ($gl->getTotal() > 0) { ?>

	<?php  include(DIR_FILES_ELEMENTS_CORE . '/search_results_top.php'); ?>

<?php  foreach ($gResults as $g) { ?>

	<div class="ccm-group">
		<a class="ccm-group-inner" href="<?php echo $this->url('/dashboard/groups?task=edit&gID=' . $g['gID'])?>" style="background-image: url(<?php echo ASSETS_URL_IMAGES?>/icons/group.png)"><?php echo $g['gName']?></a>
		<div class="ccm-group-description"><?php echo $g['gDescription']?></div>
	</div>


<?php  }

if ($pOptions['needPaging']) { ?>
	<div id="ccm-group-paging">
	<?php  include(DIR_FILES_ELEMENTS_CORE . '/search_results_paging.php'); ?>
	</div>
<?php  }


} else { ?>

	<p><?php echo t('No groups found.')?></p>
	
<?php  } ?>

</div>

<h1><span><?php echo t('Add Group')?> (<em class="required">*</em> - <?php echo t('required field')?>)</span></h1>

<div class="ccm-dashboard-inner">

<form method="post" id="add-group-form" action="<?php echo $this->url('/dashboard/groups/')?>">
<div style="margin:0px; padding:0px; width:100%; height:auto" >	
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" colspan="3"><?php echo t('Name')?> <span class="required">*</span></td>
</tr>
<tr>
	<td colspan="3"><input type="text" name="gName" style="width: 100%" value="<?php echo $_POST['gName']?>" /></td>
</tr>
<tr>
	<td class="subheader" colspan="3"><?php echo t('Description')?></td>
</tr>
<tr>
	<td colspan="3"><textarea name="gDescription" style="width: 100%; height: 120px"><?php echo $_POST['gDescription']?></textarea></td>
</tr>
<tr>
	<td colspan="3" class="header"><input type="hidden" name="add" value="1" /><?php echo $ih->submit(t('Add'), 'add-group-form')?></td>
</tr>
</table>
</div>
<br>
</form>	
</div>



<?php  } else { ?>
	<h1><span><?php echo t('Edit Group')?></span></h1>
	<div class="ccm-dashboard-inner">
	
		<form method="post" id="update-group-form" action="<?php echo $this->url('/dashboard/groups/')?>">
		<input type="hidden" name="gID" value="<?php echo $_REQUEST['gID']?>" />
		<input type="hidden" name="task" value="edit" />
		
		<div style="margin:0px; padding:0px; width:100%; height:auto" >	
		<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td class="subheader" colspan="3"><?php echo t('Name')?> <span class="required">*</span></td>
		</tr>
		<tr>
			<td colspan="3"><input type="text" name="gName" style="width: 100%" value="<?php echo $gName?>" /></td>
		</tr>
		<tr>
			<td class="subheader" colspan="3"><?php echo t('Description')?></td>
		</tr>
		<tr>
			<td colspan="3"><textarea name="gDescription" style="width: 100%; height: 120px"><?php echo $gDescription?></textarea></td>
		</tr>
		<tr>
			<td colspan="3" class="header">
			<input type="hidden" name="update" value="1" />
			<?php echo $ih->submit(t('Update'), 'update-group-form')?>
			<?php echo $ih->button(t('Cancel'), $this->url('/dashboard/groups'), 'left')?>
			</td>
		</tr>
		</table>
		</div>
		
		<br>
		</form>	
	</div>
	
	<h1><span><?php echo t('Delete Group')?></span></h1>
	
	<div class="ccm-dashboard-inner">
		<?php 
		$u=new User();

		$delConfirmJS = t('Are you sure you want to permanently remove this group?');
		if($u->isSuperUser() == false){ ?>
			<?php echo t('You must be logged in as %s to remove groups.', USER_SUPER)?>			
		<?php  }else{ ?>   

			<script type="text/javascript">
			deleteGroup = function() {
				if (confirm('<?php echo $delConfirmJS?>')) { 
					location.href = "<?php echo $this->url('/dashboard/groups', 'delete', $_REQUEST['gID'])?>";				
				}
			}
			</script>

			<?php  print $ih->button_js(t('Delete Group'), "deleteGroup", 'left');?>

		<?php  } ?>
		<div class="ccm-spacer"></div>
	</div>	
	<?php    
}
