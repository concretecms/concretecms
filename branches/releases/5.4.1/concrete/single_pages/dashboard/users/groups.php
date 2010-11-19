<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$section = 'groups';

function checkExpirationOptions($g) {
	if ($_POST['gUserExpirationIsEnabled']) {
		$date = Loader::helper('form/date_time');
		switch($_POST['gUserExpirationMethod']) {
			case 'SET_TIME':
				$g->setGroupExpirationByDateTime($date->translate('gUserExpirationSetDateTime'), $_POST['gUserExpirationAction']);
				break;
			case 'INTERVAL':
				$g->setGroupExpirationByInterval($_POST['gUserExpirationIntervalDays'], $_POST['gUserExpirationIntervalHours'], $_POST['gUserExpirationIntervalMinutes'], $_POST['gUserExpirationAction']);
				break;
		}
	} else {
		$g->removeGroupExpiration();
	}
}

if ($_REQUEST['task'] == 'edit') {
	$g = Group::getByID(intval($_REQUEST['gID']));
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
$valt = Loader::helper('validation/token');

if ($_POST['add'] || $_POST['update']) {

	$gName = $txt->sanitize($_POST['gName']);
	$gDescription = $_POST['gDescription'];
	
	$error = array();
	if (!$gName) {
		$error[] = t("Name required.");
	}
	
	if (!$valt->validate('add_or_update_group')) {
		$error[] = $valt->getErrorMessage();
	}
	
	$g1 = Group::getByName($gName);
	if ($g1 instanceof Group) {
		if ((!is_object($g)) || $g->getGroupID() != $g1->getGroupID()) {
			$error[] = t('A group named "%s" already exists', $g1->getGroupName());
		}
	}
	
	if (count($error) == 0) {
		if ($_POST['add']) {
			$g = Group::add($gName, $_POST['gDescription']);
			checkExpirationOptions($g);
			$this->controller->redirect('/dashboard/users/groups?created=1');
		} else if (is_object($g)) {
			$g->update($gName, $_POST['gDescription']);
			checkExpirationOptions($g);
			$this->controller->redirect('/dashboard/users/groups?updated=1');
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
$gl = new GroupSearch();
if (isset($_GET['gKeywords'])) {
	$gl->filterByKeywords($_GET['gKeywords']);
}

$gResults = $gl->getPage();

?>

<h1><span><?php echo t('Groups')?></span></h1>
<div class="ccm-dashboard-inner">

<?php 
$tp = new TaskPermission();
if ($tp->canAccessGroupSearch()) { ?>

<form id="ccm-group-search" method="get" style="top: -30px; left: 10px" action="<?php echo $this->url('/dashboard/users/groups')?>">
<div id="ccm-group-search-fields">
<input type="text" id="ccm-group-search-keywords" name="gKeywords" value="<?php echo htmlentities($_REQUEST['gKeywords'])?>" class="ccm-text" style="width: 100px" />
<input type="submit" value="<?php echo t('Search')?>" />
<input type="hidden" name="group_submit_search" value="1" />
</div>
</form>

<?php  if (count($gResults) > 0) { 
	$gl->displaySummary();
	
foreach ($gResults as $g) { ?>

	<div class="ccm-group">
		<a class="ccm-group-inner" href="<?php echo $this->url('/dashboard/users/groups?task=edit&gID=' . $g['gID'])?>" style="background-image: url(<?php echo ASSETS_URL_IMAGES?>/icons/group.png)"><?php echo $g['gName']?></a>
		<div class="ccm-group-description"><?php echo $g['gDescription']?></div>
	</div>


<?php  }

	$gl->displayPaging();

} else { ?>

	<p><?php echo t('No groups found.')?></p>
	
<?php  } ?>

<?php  } else { ?>
	<p><?php echo t('You do not have access to group search. This setting may be changed in the access section of the dashboard settings page.')?></p>

<?php  } ?>
</div>

<h1><span><?php echo t('Add Group')?> (<em class="required">*</em> - <?php echo t('required field')?>)</span></h1>

<div class="ccm-dashboard-inner">

<form method="post" id="add-group-form" action="<?php echo $this->url('/dashboard/users/groups/')?>">
<?php echo $valt->output('add_or_update_group')?>
<div style="margin:0px; padding:0px; width:100%; height:auto" >	
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?php echo t('Name')?> <span class="required">*</span></td>
</tr>
<tr>
	<td><input type="text" name="gName" style="width: 100%" value="<?php echo htmlentities($_POST['gName'])?>" /></td>
</tr>
<tr>
	<td class="subheader"><?php echo t('Description')?></td>
</tr>
<tr>
	<td><textarea name="gDescription" style="width: 100%; height: 120px"><?php echo $_POST['gDescription']?></textarea></td>
</tr>
<tr>
	<td class="subheader"><?php echo t("Group Expiration Options")?></td>
</tr>
<?php  $form = Loader::helper('form'); ?>
<?php  $date = Loader::helper('form/date_time'); ?>
<?php 
$style = 'width: 60px';
?>
<tr>	
	<td><?php echo $form->checkbox('gUserExpirationIsEnabled', 1, false)?>
	<?php echo t('Automatically remove users from this group')?>
	
	<?php echo $form->select("gUserExpirationMethod", array(
		'SET_TIME' => t('at a specific date and time'),
			'INTERVAL' => t('once a certain amount of time has passed')
		
	), array('disabled' => true));?>	
	
	<div id="gUserExpirationSetTimeOptions" style="display: none">
	<br/>
	<h2><?php echo t('Expiration Date')?></h2>
	<?php echo $date->datetime('gUserExpirationSetDateTime')?>
	</div>
	<div id="gUserExpirationIntervalOptions" style="display: none">
	<br/>
	<h2><?php echo t('Accounts will Expire After')?></h2>
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top"><strong><?php echo t('Days')?></strong><br/>
		<?php echo $form->text('gUserExpirationIntervalDays', array('style' => $style))?>
		</td>
		<td valign="top"><strong><?php echo t('Hours')?></strong><br/>
		<?php echo $form->text('gUserExpirationIntervalHours', array('style' => $style))?>
		</td>
		<td valign="top"><strong><?php echo t('Minutes')?></strong><br/>
		<?php echo $form->text('gUserExpirationIntervalMinutes', array('style' => $style))?>
		</td>
	</tr>
	</table>
	</div>
	<div id="gUserExpirationAction" style="display: none">
	<br/>
	<h2><?php echo t('Expiration Action')?></h2>
		<?php echo $form->select("gUserExpirationAction", array(
		'REMOVE' => t('Remove the user from this group'),
			'DEACTIVATE' => t('Deactivate the user account'),
			'REMOVE_DEACTIVATE' => t('Remove the user from the group and deactivate the account')
		
	));?>	

	</div>
	</td>
</tr>
<tr>
	<td class="header"><input type="hidden" name="add" value="1" /><?php echo $ih->submit(t('Add'), 'add-group-form')?></td>
</tr>
</table>
</div>
<br>
</form>	
</div>

<?php  } else { ?>
	<h1><span><?php echo t('Edit Group')?></span></h1>
	<div class="ccm-dashboard-inner">
	
		<form method="post" id="update-group-form" action="<?php echo $this->url('/dashboard/users/groups/')?>">
		<?php echo $valt->output('add_or_update_group')?>
		<input type="hidden" name="gID" value="<?php echo intval($_REQUEST['gID'])?>" />
		<input type="hidden" name="task" value="edit" />
		
		<div style="margin:0px; padding:0px; width:100%; height:auto" >	
		<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td class="subheader"><?php echo t('Name')?> <span class="required">*</span></td>
		</tr>
		<tr>
			<td><input type="text" name="gName" style="width: 100%" value="<?php echo $gName?>" /></td>
		</tr>
		<tr>
			<td class="subheader"><?php echo t('Description')?></td>
		</tr>
		<tr>
			<td><textarea name="gDescription" style="width: 100%; height: 120px"><?php echo $gDescription?></textarea></td>
		</tr>
		<tr>
	<td class="subheader"><?php echo t("Group Expiration Options")?></td>
</tr>
<?php  $form = Loader::helper('form'); ?>
<?php  $date = Loader::helper('form/date_time'); ?>
<tr>	
	<td><?php echo $form->checkbox('gUserExpirationIsEnabled', 1, $g->isGroupExpirationEnabled())?>
	<?php echo t('Automatically remove users from this group')?>
	
	<?php echo $form->select("gUserExpirationMethod", array(
		'SET_TIME' => t('at a specific date and time'),
			'INTERVAL' => t('once a certain amount of time has passed')
		
	), $g->getGroupExpirationMethod(), array('disabled' => true));?>	
	
	<div id="gUserExpirationSetTimeOptions" style="display: none">
	<br/>
	<h2><?php echo t('Expiration Date')?></h2>
	<?php echo $date->datetime('gUserExpirationSetDateTime', $g->getGroupExpirationDateTime())?>
	</div>
	<div id="gUserExpirationIntervalOptions" style="display: none">
	<br/>
	<h2><?php echo t('Accounts will Expire After')?></h2>
	<?php 
	$days = $g->getGroupExpirationIntervalDays();
	$hours = $g->getGroupExpirationIntervalHours();
	$minutes = $g->getGroupExpirationIntervalMinutes();
	
	/*
	if ($days == 0 && $hours == 0 && $minutes == 0) {
		$days = t('Days');
		$hours = t('Hours');
		$minutes =t('Minutes');
		$style = 'width: 60px; color: #aaa';
	} else {
		$style = 'width: 60px';
	}
	*/
	$style = 'width: 60px';
	?>
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top"><strong><?php echo t('Days')?></strong><br/>
		<?php echo $form->text('gUserExpirationIntervalDays', $days, array('style' => $style))?>
		</td>
		<td valign="top"><strong><?php echo t('Hours')?></strong><br/>
		<?php echo $form->text('gUserExpirationIntervalHours', $hours, array('style' => $style))?>
		</td>
		<td valign="top"><strong><?php echo t('Minutes')?></strong><br/>
		<?php echo $form->text('gUserExpirationIntervalMinutes', $minutes, array('style' => $style))?>
		</td>
	</tr>
	</table>
	</div>
	<div id="gUserExpirationAction" style="display: none">
	<br/>
	<h2><?php echo t('Expiration Action')?></h2>
		<?php echo $form->select("gUserExpirationAction", array(
		'REMOVE' => t('Remove the user from this group'),
			'DEACTIVATE' => t('Deactivate the user account'),
			'REMOVE_DEACTIVATE' => t('Remove the user from the group and deactivate the account')
		
	), $g->getGroupExpirationAction());?>	

	</div>
	</td>
</tr>

		<tr>
			<td class="header">
			<input type="hidden" name="update" value="1" />
			<?php echo $ih->submit(t('Update'), 'update-group-form')?>
			<?php echo $ih->button(t('Cancel'), $this->url('/dashboard/users/groups'), 'left')?>
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
					location.href = "<?php echo $this->url('/dashboard/users/groups', 'delete', intval($_REQUEST['gID']), $valt->generate('delete_group_' . intval($_REQUEST['gID']) ))?>";				
				}
			}
			</script>

			<?php  print $ih->button_js(t('Delete Group'), "deleteGroup()", 'left');?>

		<?php  } ?>
		<div class="ccm-spacer"></div>
	</div>	
	<?php    
}

?>

<script type="text/javascript">
ccm_checkGroupExpirationOptions = function() {
	var sel = $("select[name=gUserExpirationMethod]");
	var cb = $("input[name=gUserExpirationIsEnabled]");
	if (cb.attr('checked')) {
		sel.attr('disabled', false);
		switch(sel.val()) {
			case 'SET_TIME':
				$("#gUserExpirationSetTimeOptions").show();
				$("#gUserExpirationIntervalOptions").hide();
				break;
			case 'INTERVAL': 
				$("#gUserExpirationSetTimeOptions").hide();
				$("#gUserExpirationIntervalOptions").show();
				break;				
		}
		$("#gUserExpirationAction").show();
	} else {
		sel.attr('disabled', true);	
		$("#gUserExpirationSetTimeOptions").hide();
		$("#gUserExpirationIntervalOptions").hide();
		$("#gUserExpirationAction").hide();
	}
}

$(function() {
	$("input[name=gUserExpirationIsEnabled]").click(ccm_checkGroupExpirationOptions);
	$("select[name=gUserExpirationMethod]").change(ccm_checkGroupExpirationOptions);
	ccm_checkGroupExpirationOptions();
	/*
	$("div#gUserExpirationIntervalOptions input").focus(function() {
		if ($('input[name=gUserExpirationIntervalDays]').val() == '<?php echo t("Days")?>' &&
			$('input[name=gUserExpirationIntervalHours]').val() == '<?php echo t("Hours")?>' &&
			$('input[name=gUserExpirationIntervalMinutes]').val() == '<?php echo t("Minutes")?>') {
			$("div#gUserExpirationIntervalOptions input").val("");
			$("div#gUserExpirationIntervalOptions input").css('color', '#000');
		}
	});
	$("div#gUserExpirationIntervalOptions input").blur(function() {
		if ($('input[name=gUserExpirationIntervalDays]').val() == '' &&
			$('input[name=gUserExpirationIntervalHours]').val() == '' &&
			$('input[name=gUserExpirationIntervalMinutes]').val() == '') {
			$('input[name=gUserExpirationIntervalDays]').val('<?php echo t("Days")?>');
			$('input[name=gUserExpirationIntervalHours]').val('<?php echo t("Hours")?>');
			$('input[name=gUserExpirationIntervalMinutes]').val('<?php echo t("Minutes")?>');
			$("div#gUserExpirationIntervalOptions input").css('color', '#aaa');
		}
	});
	*/
});
</script>

