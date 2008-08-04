<?
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
		$error[] = "Name required.";
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
	$message = "Group Created.";
} else if ($_GET['updated']) {
	$message = "Group Updated.";
}

$gl = new GroupList('', true);
$gArray = $gl->getGroupList();

if ($editMode) { ?>	
<h1><span>Edit Group</span></h1>
<div class="ccm-dashboard-inner">

	<form method="post" id="update-group-form" action="<?=$this->url('/dashboard/groups/')?>">
	<input type="hidden" name="gID" value="<?=$_REQUEST['gID']?>" />
	<input type="hidden" name="task" value="edit" />
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" colspan="3">Name <span class="required">*</span></td>
	</tr>
	<tr>
		<td colspan="3"><input type="text" name="gName" style="width: 100%" value="<?=$gName?>" /></td>
	</tr>
	<tr>
		<td class="subheader" colspan="3">Description</td>
	</tr>
	<tr>
		<td colspan="3"><textarea name="gDescription" style="width: 100%; height: 120px"><?=$gDescription?></textarea></td>
	</tr>
	<tr>
		<td colspan="3" class="header">
		<input type="hidden" name="update" value="1" />
		<?=$ih->submit('Add', 'update-group-form')?>
		<?=$ih->button('Cancel', $this->url('/dashboard/groups'), 'left')?>
		</td>
	</tr>
	</table>
	</div>
	
	<br>
	</form>	
</div>
<? 

} else { ?>

<h1><span>Groups</span></h1>
<div class="ccm-dashboard-inner">


<?
if (count($gArray) == 0) { ?>
	
	<br/><strong>No groups defined.</strong>
	
<? } else { ?>
<ul class="ccm-dashboard-list">
<? 
foreach($gArray as $g) { ?>

<li><a href="<?=$this->url('/dashboard/groups?gID=' . $g->getGroupID() . '&task=edit')?>" title="<?=$g->getGroupDescription()?>" ><?=$g->getGroupName()?></a></li>

<? } ?>

</ul>


<?

}?>

</div>

<h1><span>Add Group (<em class="required">*</em> - required field)</span></h1>

<div class="ccm-dashboard-inner">

<form method="post" id="add-group-form" action="<?=$this->url('/dashboard/groups/')?>">
<div style="margin:0px; padding:0px; width:100%; height:auto" >	
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" colspan="3">Name <span class="required">*</span></td>
</tr>
<tr>
	<td colspan="3"><input type="text" name="gName" style="width: 100%" value="<?=$_POST['gName']?>" /></td>
</tr>
<tr>
	<td class="subheader" colspan="3">Description</td>
</tr>
<tr>
	<td colspan="3"><textarea name="gDescription" style="width: 100%; height: 120px"><?=$_POST['gDescription']?></textarea></td>
</tr>
<tr>
	<td colspan="3" class="header"><input type="hidden" name="add" value="1" /><?=$ih->submit('Add', 'add-group-form')?></td>
</tr>
</table>
</div>
<br>
</form>	
</div>


<? } ?>

<br/>