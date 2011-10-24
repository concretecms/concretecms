<?
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

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Groups'), false, 'span12 offset2', false)?>
<?
$tp = new TaskPermission();
if ($tp->canAccessGroupSearch()) { ?>

<div class="ccm-pane-options">
<div class="ccm-pane-options-permanent-search">
<form method="get" action="<?=$this->url('/dashboard/users/groups')?>">
<div class="span7">
<? $form = Loader::helper('form'); ?>
<?=$form->label('gKeywords', t('Keywords'))?>
<div class="input">
	<input type="text" name="gKeywords" value="<?=htmlentities($_REQUEST['gKeywords'])?>"  />
	<input class="btn" type="submit" value="<?=t('Search')?>" />
</div>
<input type="hidden" name="group_submit_search" value="1" />
</div>
</form>
</div>
</div>
<div class="ccm-pane-body <? if (!$gl->requiresPaging()) { ?> ccm-pane-body-footer <? } ?>">

<? if (count($gResults) > 0) { 
	$gl->displaySummary();
	
foreach ($gResults as $g) { ?>

	<div class="ccm-group">
		<a class="ccm-group-inner" href="<?=$this->url('/dashboard/users/groups?task=edit&gID=' . $g['gID'])?>" style="background-image: url(<?=ASSETS_URL_IMAGES?>/icons/group.png)"><?=$g['gName']?></a>
		<? if ($g['gDescription']) { ?>
			<div class="ccm-group-description"><?=$g['gDescription']?></div>
		<? } ?>
	</div>


<? }

} else { ?>

	<p><?=t('No groups found.')?></p>
	
<? } ?>
</div>
<? if ($gl->requiresPaging()) { ?>
<div class="ccm-pane-footer">
	<?=$gl->displayPaging();?>
</div>
<? } ?>

<? } else { ?>
<div class="ccm-pane-body ccm-pane-body-footer">
	<p><?=t('You do not have access to group search. This setting may be changed in the access section of the dashboard settings page.')?></p>
</div>
<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<? } else { ?>
	<h1><span><?=t('Edit Group')?></span></h1>
	<div class="ccm-dashboard-inner">
	
		<form method="post" id="update-group-form" action="<?=$this->url('/dashboard/users/groups/')?>">
		<?=$valt->output('add_or_update_group')?>
		<input type="hidden" name="gID" value="<?=intval($_REQUEST['gID'])?>" />
		<input type="hidden" name="task" value="edit" />
		
		<div style="margin:0px; padding:0px; width:100%; height:auto" >	
		<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td class="subheader"><?=t('Name')?> <span class="required">*</span></td>
		</tr>
		<tr>
			<td><input type="text" name="gName" style="width: 100%" value="<?=$gName?>" /></td>
		</tr>
		<tr>
			<td class="subheader"><?=t('Description')?></td>
		</tr>
		<tr>
			<td><textarea name="gDescription" style="width: 100%; height: 120px"><?=$gDescription?></textarea></td>
		</tr>
		<tr>
	<td class="subheader"><?=t("Group Expiration Options")?></td>
</tr>
<? $form = Loader::helper('form'); ?>
<? $date = Loader::helper('form/date_time'); ?>
<tr>	
	<td><?=$form->checkbox('gUserExpirationIsEnabled', 1, $g->isGroupExpirationEnabled())?>
	<?=t('Automatically remove users from this group')?>
	
	<?=$form->select("gUserExpirationMethod", array(
		'SET_TIME' => t('at a specific date and time'),
			'INTERVAL' => t('once a certain amount of time has passed')
		
	), $g->getGroupExpirationMethod(), array('disabled' => true));?>	
	
	<div id="gUserExpirationSetTimeOptions" style="display: none">
	<br/>
	<h2><?=t('Expiration Date')?></h2>
	<?=$date->datetime('gUserExpirationSetDateTime', $g->getGroupExpirationDateTime())?>
	</div>
	<div id="gUserExpirationIntervalOptions" style="display: none">
	<br/>
	<h2><?=t('Accounts will Expire After')?></h2>
	<?
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
		<td valign="top"><strong><?=t('Days')?></strong><br/>
		<?=$form->text('gUserExpirationIntervalDays', $days, array('style' => $style))?>
		</td>
		<td valign="top"><strong><?=t('Hours')?></strong><br/>
		<?=$form->text('gUserExpirationIntervalHours', $hours, array('style' => $style))?>
		</td>
		<td valign="top"><strong><?=t('Minutes')?></strong><br/>
		<?=$form->text('gUserExpirationIntervalMinutes', $minutes, array('style' => $style))?>
		</td>
	</tr>
	</table>
	</div>
	<div id="gUserExpirationAction" style="display: none">
	<br/>
	<h2><?=t('Expiration Action')?></h2>
		<?=$form->select("gUserExpirationAction", array(
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
			<?=$ih->submit(t('Update'), 'update-group-form')?>
			<?=$ih->button(t('Cancel'), $this->url('/dashboard/users/groups'), 'left')?>
			</td>
		</tr>
		</table>
		</div>
		
		<br>
		</form>	
	</div>
	
	<h1><span><?=t('Delete Group')?></span></h1>
	
	<div class="ccm-dashboard-inner">
		<?
		$u=new User();

		$delConfirmJS = t('Are you sure you want to permanently remove this group?');
		if($u->isSuperUser() == false){ ?>
			<?=t('You must be logged in as %s to remove groups.', USER_SUPER)?>			
		<? }else{ ?>   

			<script type="text/javascript">
			deleteGroup = function() {
				if (confirm('<?=$delConfirmJS?>')) { 
					location.href = "<?=$this->url('/dashboard/users/groups', 'delete', intval($_REQUEST['gID']), $valt->generate('delete_group_' . intval($_REQUEST['gID']) ))?>";				
				}
			}
			</script>

			<? print $ih->button_js(t('Delete Group'), "deleteGroup()", 'left');?>

		<? } ?>
		<div class="ccm-spacer"></div>
	</div>	
	<?   
}

?>

<script type="text/javascript">
ccm_checkGroupExpirationOptions = function() {
	var sel = $("select[name=gUserExpirationMethod]");
	var cb = $("input[name=gUserExpirationIsEnabled]");
	if (cb.prop('checked')) {
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
		if ($('input[name=gUserExpirationIntervalDays]').val() == '<?=t("Days")?>' &&
			$('input[name=gUserExpirationIntervalHours]').val() == '<?=t("Hours")?>' &&
			$('input[name=gUserExpirationIntervalMinutes]').val() == '<?=t("Minutes")?>') {
			$("div#gUserExpirationIntervalOptions input").val("");
			$("div#gUserExpirationIntervalOptions input").css('color', '#000');
		}
	});
	$("div#gUserExpirationIntervalOptions input").blur(function() {
		if ($('input[name=gUserExpirationIntervalDays]').val() == '' &&
			$('input[name=gUserExpirationIntervalHours]').val() == '' &&
			$('input[name=gUserExpirationIntervalMinutes]').val() == '') {
			$('input[name=gUserExpirationIntervalDays]').val('<?=t("Days")?>');
			$('input[name=gUserExpirationIntervalHours]').val('<?=t("Hours")?>');
			$('input[name=gUserExpirationIntervalMinutes]').val('<?=t("Minutes")?>');
			$("div#gUserExpirationIntervalOptions input").css('color', '#aaa');
		}
	});
	*/
});
</script>

