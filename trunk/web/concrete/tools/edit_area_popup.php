<?
$c = Page::getByID($_REQUEST['cID']);
$cp = new Permissions($c);
$a = Area::get($c, $_GET['arHandle']);
$ap = new Permissions($a);

$btl = $a->getAddBlockTypes($c, $ap );
$blockTypes = $btl->getBlockTypeList();
$ci = Loader::helper('concrete/urls');

?>

<ul class="ccm-dialog-tabs" id="ccm-area-tabs">
<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-add">Add New</a></li>
<li><a href="javascript:void(0)" id="ccm-add-existing">Add From Scrapbook</a></li>
<? if (PERMISSIONS_MODEL != 'simple') { ?><li><a href="javascript:void(0)" id="ccm-permissions">Permissions</a></li><? } ?>
</ul>

<div id="ccm-add-tab">
<h1>Add New Block</h1>
<div id="ccm-block-type-list">
<? if (count($blockTypes) > 0) {

	foreach($blockTypes as $bt) { 
		$btIcon = $ci->getBlockTypeIconURL($bt);
		?>
	
	<div class="ccm-block-type">
		<a class="ccm-block-type-help" href="javascript:ccm_showBlockTypeDescription(<?=$bt->getBlockTypeID()?>)" title="Learn more about this block type." id="ccm-bt-help-trigger<?=$bt->getBlockTypeID()?>"><img src="<?=ASSETS_URL_IMAGES?>/icons/help.png" width="14" height="14" /></a>
		<a class="dialog-launch ccm-block-type-inner" dialog-modal="true" dialog-width="<?=$bt->getBlockTypeInterfaceWidth()?>" dialog-height="<?=$bt->getBlockTypeInterfaceHeight()?>" style="background-image: url(<?=$btIcon?>)" dialog-title="Add <?=$bt->getBlockTypeName()?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/add_block_popup.php?cID=<?=$c->getCollectionID()?>&btID=<?=$bt->getBlockTypeID()?>&arHandle=<?=$a->getAreaHandle()?>"><?=$bt->getBlockTypeName()?></a>
		<div class="ccm-block-type-description"  id="ccm-bt-help<?=$bt->getBlockTypeID()?>"><?=$bt->getBlockTypeDescription()?></div>
	</div>
	<? }
} else { ?>
	<p>No block types can be added to this area.</p>
<? } ?>
</div>
</div>

<script type="text/javascript">
ccm_showBlockTypeDescription = function(btID) {
	$("#ccm-bt-help" + btID).show();
}

var ccm_areaActiveTab = "ccm-add";

$("#ccm-area-tabs a").click(function() {
	$("li.ccm-nav-active").removeClass('ccm-nav-active');
	$("#" + ccm_areaActiveTab + "-tab").hide();
	ccm_areaActiveTab = $(this).attr('id');
	$(this).parent().addClass("ccm-nav-active");
	$("#" + ccm_areaActiveTab + "-tab").show();
});

$(function() {

	$("a.ccm-scrapbook-delete").click(function() {

		var pcID = $(this).attr('id').substring(2);
		$.ajax({
			type: 'POST',
			url: CCM_DISPATCHER_FILENAME,
			data: 'pcID=' + pcID + '&ptask=delete_content',
			success: function(msg) {
				$("#ccm-pc-" + pcID).fadeOut();
			}
		});
		
	});

});
</script>

<div id="ccm-add-existing-tab" style="display:none">
<h1>Add From Scrapbook</h1>
<div id="ccm-scrapbook-list">
<?
Loader::model('pile');
$sp = Pile::getDefault();
$contents = $sp->getPileContentObjects('date_desc');
if (count($contents) == 0) { 
	print 'You have no items in your scrapbook.';
}
foreach($contents as $obj) { 
	$item = $obj->getObject();
	if (is_object($item)) {
	$bt = $item->getBlockTypeObject();
		$btIcon = $ci->getBlockTypeIconURL($bt);
?>
	
	<div class="ccm-scrapbook-list-item" id="ccm-pc-<?=$obj->getPileContentID()?>">
	<div class="ccm-block-type">
		<a class="ccm-scrapbook-delete" title="Remove from Scrapbook" href="javascript:void(0)" id="sb<?=$obj->getPileContentID()?>"><img src="<?=ASSETS_URL_IMAGES?>/icons/close.png" width="14" height="14" /></a>
		<a class="ccm-block-type-inner" style="background-image: url(<?=$btIcon?>)" href="<?=DIR_REL?>/index.php?pcID[]=<?=$obj->getPileContentID()?>&add=1&processBlock=1&cID=<?=$c->getCollectionID()?>&arHandle=<?=$a->getAreaHandle()?>&btask=alias_existing_block"><?=$bt->getBlockTypeName()?></a>
	<div class="ccm-scrapbook-list-item-detail">	
	<?
	
	try {
		$bv = new BlockView();
		$bv->render($item);
	} catch(Exception $e) {
		print BLOCK_NOT_AVAILABLE_TEXT;
	}
	
	?>

	</div>
	</div>
	</div>
	
	<?
	$i++;
}	
	
}	?>
</div>
</div>

<div id="ccm-permissions-tab" style="display: none">
<h1>Set Area Permissions</h1>

<?

if ($cp->canAdminPage() && is_object($a)) {

	$btArray = BlockTypeList::getAreaBlockTypes($a, $cp);
	
	
	// if the area overrides the collection permissions explicitly (with a one on the override column) we check
	
	if ($a->overrideCollectionPermissions()) {
		$gl = new GroupList($a);
		$ul = new UserInfoList($a);
	} else {
		// now it gets more complicated. 
		$permsSet = false;
		
		if ($a->getAreaCollectionInheritID() > 0) {
			// in theory we're supposed to be inheriting some permissions from an area with the same handle,
			// set on the collection id specified above (inheritid). however, if someone's come along and
			// reverted that area to the page's permissions, there won't be any permissions, and we 
			// won't see anything. so we have to check
			$areac = Page::getByID($a->getAreaCollectionInheritID());
			$inheritArea = Area::get($areac, $_GET['arHandle']);
			if ($inheritArea->overrideCollectionPermissions()) {
				// okay, so that area is still around, still has set permissions on it. So we
				// pass our current area to our grouplist, userinfolist objects, knowing that they will 
				// smartly inherit the correct items.
				$gl = new GroupList($a);
				$ul = new UserInfoList($a);
				$permsSet = true;				
			}
		}		
		
		if (!$permsSet) {
			// otherwise we grab the collection permissions for this page
			$gl = new GroupList($c);
			$ul = new UserInfoList($c);
		}	
	}
	
	$gArray = $gl->getGroupList();
	$ulArray = $ul->getUserInfoList();
	
	?>
	<script type="text/javascript">
		function ccm_triggerSelectUser(uID, uName) {
			rowValue = "uID:" + uID;
			rowText = uName;
			if ($("#_row_uID_" + uID).length > 0) {
				return false;
			}			

			tbl = document.getElementById("ccmPermissionsTable");	   
			row1 = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
			row2 = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
			row3 = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?    
			
			row1.className = rowValue.replace(":","_");
			row1.id = "_row_uID_" + uID;
			row2.className = rowValue.replace(":","_");
			row3.className = rowValue.replace(":","_");
			
			row1innerHTML = '<th colspan="7" style="text-align: left; white-space: nowrap"><a href="javascript:removePermissionRow(\'' + rowValue.replace(':','_') + '\',\'' + rowText + '\')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12" style="float: right" ></a>' + uName + '</td>';
			row2innerHTML = '<td valign="top" style="text-align: center"><strong>Read</strong></td>';
			row2innerHTML += '<td valign="top" ><input type="checkbox" name="areaRead[]" value="' + rowValue + '"></td>';
			row2innerHTML += '<td><div style="width: 54px; text-align: right"><strong>Write</strong></div></td><td><input type="checkbox" name="areaEdit[]" value="' + rowValue + '"></td>';
			row2innerHTML += '<td><div style="width: 54px; text-align: right"><strong>Delete</strong></div></td><td><input type="checkbox" name="areaDelete[]" value="' + rowValue + '"></td>';
			row2innerHTML += '<td valign="top" width="100%"><div style="width: 225px">&nbsp;</div></td>';
			row3innerHTML = '<td valign="top"  style="text-align: center"><strong>Add</strong></td>';
			row3innerHTML += '<td colspan="6" width="100%"><div style="width: 460px;">';
			<? foreach ($btArray as $bt) { ?>
				row3innerHTML += '<span style="white-space: nowrap; float: left; width: 80px; margin-right: 20px"><input type="checkbox" name="areaAddBlockType[<?=$bt->getBlockTypeID()?>][]" value="' + rowValue + '" />&nbsp;<?=$bt->getBlockTypeName()?></span>';
			<? } ?>	
			row3innerHTML += '</div></td>';
			
			row1.innerHTML = row1innerHTML;
			row2.innerHTML = row2innerHTML;
			row3.innerHTML = row3innerHTML;
			
			tbl.appendChild(row1);
			tbl.appendChild(row2);
			tbl.appendChild(row3);
		}
		
		function ccm_addGroup(gID, gName) {
			// we add a row for the selected group
			var rowText = gName;
			var rowValue = "gID:" + gID;
			
			if ($("#_row_gID_" + gID).length > 0) {
				return false;
			}
			
			tbl = document.getElementById("ccmPermissionsTable");	   
			row1 = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
			row1.id = "_row_gID_" + gID;
			row2 = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
			row3 = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?    
			
			row1.className = rowValue.replace(":","_");
			row2.className = rowValue.replace(":","_");
			row3.className = rowValue.replace(":","_");
			
			row1innerHTML = '<th colspan="7" style="text-align: left; white-space: nowrap"><a href="javascript:removePermissionRow(\'' + rowValue.replace(':','_') + '\',\'' + rowText + '\')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12" style="float: right" ></a>' + rowText + '</td>';
			row2innerHTML = '<td valign="top" style="text-align: center"><strong>Read</strong></td>';
			row2innerHTML += '<td valign="top" ><input type="checkbox" name="areaRead[]" value="' + rowValue + '"></td>';
			row2innerHTML += '<td><div style="width: 54px; text-align: right"><strong>Write</strong></div></td><td><input type="checkbox" name="areaEdit[]" value="' + rowValue + '"></td>';
			row2innerHTML += '<td><div style="width: 54px; text-align: right"><strong>Delete</strong></div></td><td><input type="checkbox" name="areaDelete[]" value="' + rowValue + '"></td>';
			row2innerHTML += '<td valign="top" width="100%"><div style="width: 225px">&nbsp;</div></td>';
			row3innerHTML = '<td valign="top"  style="text-align: center"><strong>Add</strong></td>';
			row3innerHTML += '<td colspan="6" width="100%"><div style="width: 460px;">';
			<? foreach ($btArray as $bt) { ?>
				row3innerHTML += '<span style="white-space: nowrap; float: left; width: 80px; margin-right: 20px"><input type="checkbox" name="areaAddBlockType[<?=$bt->getBlockTypeID()?>][]" value="' + rowValue + '" />&nbsp;<?=$bt->getBlockTypeName()?></span>';
			<? } ?>	
			row3innerHTML += '</div></td>';
			
			row1.innerHTML = row1innerHTML;
			row2.innerHTML = row2innerHTML;
			row3.innerHTML = row3innerHTML;
			
			tbl.appendChild(row1);
			tbl.appendChild(row2);
			tbl.appendChild(row3);
			
		}
		
		function removePermissionRow(rowID, origText) {
			$("." + rowID + " input[type=checkbox]").each(function() {
				this.checked = false;
			});
			$("." + rowID).hide();

    	  if (rowID && origText) {
        	  select = document.getElementById("groupSelect");
        	  currentLength = select.options.length;
        	  optionDivider = select.options[currentLength - 2];
	       	  optionAddUser = select.options[currentLength - 1];
	
	       	  select.options[currentLength - 2] = new Option(origText, rowID.replace('_',':'));
	       	  select.options[currentLength - 1] = optionDivider;
	       	  select.options[currentLength] = optionAddUser;
		  }

		}
		
		function setPermissionAvailability(value) {
			tbl = document.getElementById("ccmPermissionsTable");
			switch(value) {
				case "OVERRIDE":
					inputs = tbl.getElementsByTagName("INPUT");
					for (i = 0; i < inputs.length; i++) {
						inputs[i].disabled = false;
					}
					document.getElementById("groupSelect").disabled = false;
					break;
				default:
					inputs = tbl.getElementsByTagName("INPUT");
					for (i = 0; i < inputs.length; i++) {
						inputs[i].disabled = true;
					}
					document.getElementById("groupSelect").disabled = true;
					break;
			}
		}
		
	</script>

<form method="post" name="permissionForm" action="<?=$a->getAreaUpdateAction()?>">
<? 
if ($a->getAreaCollectionInheritID() != $c->getCollectionID() && $a->getAreaCollectionInheritID() > 0) {
	$pc = $c->getPermissionsCollectionObject(); 
	$areac = Page::getByID($a->getAreaCollectionInheritID());
?>

The following area permissions are inherited from an area set on <a href="<?=DIR_REL?>/index.php?cID=<?=$areac->getCollectionID()?>"><?=$areac->getCollectionName()?></a>. To change them everywhere, edit this area on that page. To override them here and on all sub-pages, edit below.</p>

<? } else if (!$a->overrideCollectionPermissions()) {

?>

The following area permissions are inherited from the page's permissions. To override them, edit below.

<? } else { ?>

<span class="ccm-important">
	Permissions for this area currently override those of the page. To revert to the page's permissions, click <strong>revert to page permissions</strong> below.<br/><br/>
</span>

<? } ?>

<div class="ccm-buttons" style="margin-bottom: 10px"> 
<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector.php?cID=<?=$_REQUEST['cID']?>" dialog-width="600" dialog-title="Choose User/Group"  dialog-height="400" class="dialog-launch ccm-button-right"><span><em class="ccm-button-add">Add Group or User</em></span></a>
</div>
<div class="ccm-spacer">&nbsp;</div><br/>

<table id="ccmPermissionsTable" border="0" cellspacing="0" cellpadding="0" class="ccm-grid" style="width: 100%">
<? 

$rowNum = 1;
foreach ($gArray as $g) { 
$displayRow = false;
$display = (($g->getGroupID() == GUEST_GROUP_ID || $g->getGroupID() == REGISTERED_GROUP_ID) || $g->canRead() || $g->canWrite() || $g->canAddBlocks() || $g->canDeleteBlock()) 
? true : false;

if ($display) { ?>


<tr class="gID_<?=$g->getGroupID()?>" id="_row_gID_<?=$g->getGroupID()?>">
<th colspan="7" style="text-align: left; white-space: nowrap"><? if ($g->getGroupID() != GUEST_GROUP_ID && $g->getGroupID() != REGISTERED_GROUP_ID) { ?>    
			<a href="javascript:removePermissionRow('gID_<?=$g->getGroupID()?>', '<?=$g->getGroupName()?>')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12" style="float: right" ></a>
		<? } ?>
		<?=$g->getGroupName()?></th>		
</tr>
<tr class="gID_<?=$g->getGroupID()?>">
<td valign="top" style="text-align: center"><strong>Read</strong></td>
<td valign="top" ><input type="checkbox" name="areaRead[]" value="gID:<?=$g->getGroupID()?>"<? if ($g->canRead()) { ?> checked<? } ?>></td>
<td><div style="width: 54px; text-align: right"><strong>Write</strong></div></td>
<td><input type="checkbox" name="areaEdit[]" value="gID:<?=$g->getGroupID()?>"<? if ($g->canWrite()) { ?> checked<? } ?>></td>
<td><div style="width: 54px; text-align: right"><strong>Delete</strong></div></td>
<td><input type="checkbox" name="areaDelete[]" value="gID:<?=$g->getGroupID()?>"<? if ($g->canDeleteBlock()) { ?> checked<? } ?>></td>
<td valign="top" width="100%"><div style="width: 225px">&nbsp;</div></td>
</tr>
<tr class="gID_<?=$g->getGroupID()?>">
<td valign="top"  style="text-align: center"><strong>Add</strong></td>
<td colspan="6" width="100%">
<div style="width: 460px;">
<? foreach ($btArray as $bt) { ?>
		<span style="white-space: nowrap; float: left; width: 80px; margin-right: 20px"><input type="checkbox" name="areaAddBlockType[<?=$bt->getBlockTypeID()?>][]" value="gID:<?=$g->getGroupID()?>"<? if ($bt->canAddBlock($g)) { ?> checked<? } ?>>&nbsp;<?=$bt->getBlockTypeName()?></span>
		
	<? } ?>
</div>
</td>
</tr>

<? 
$rowNum++;
} ?>
<?  }
	
foreach ($ulArray as $ui) { ?>

<tr id="_row_uID_<?=$ui->getUserID()?>" class="uID_<?=$ui->getUserID()?>" class="no-bg">

<th colspan="7" style="text-align: left; white-space: nowrap">
<a href="javascript:removePermissionRow('uID_<?=$ui->getUserID()?>')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12" style="float: right"></a>
<?=$ui->getUserName()?>
</th>		
</tr>
<tr class="uID_<?=$ui->getUserID()?>" >
<td valign="top" style="text-align: center"><strong>Read</strong></td>
<td valign="top" ><input type="checkbox" name="areaRead[]" value="uID:<?=$ui->getUserID()?>"<? if ($ui->canRead()) { ?> checked<? } ?>></td>
<td><div style="width: 54px; text-align: right"><strong>Write</strong></div></td>
<td><input type="checkbox" name="areaEdit[]" value="uID:<?=$ui->getUserID()?>"<? if ($ui->canWrite()) { ?> checked<? } ?>></td>
<td><div style="width: 54px; text-align: right"><strong>Delete</strong></div></td>
<td><input type="checkbox" name="areaDelete[]" value="uID:<?=$ui->getUserID()?>"<? if ($ui->canDeleteBlock()) { ?> checked<? } ?>></td>
<td valign="top" width="100%"><div style="width: 225px">&nbsp;</div></td>
</tr>
<tr class="uID_<?=$ui->getUserID()?>" >
<td valign="top"  style="text-align: center"><strong>Add</strong></td>
<td colspan="6" width="100%">
<div style="width: 460px;">
<? foreach ($btArray as $bt) { ?>
		<span style="white-space: nowrap; float: left; width: 80px; margin-right: 20px"><input type="checkbox" name="areaAddBlockType[<?=$bt->getBlockTypeID()?>][]" value="uID:<?=$ui->getUserID()?>"<? if ($bt->canAddBlock($ui)) { ?> checked<? } ?>>&nbsp;<?=$bt->getBlockTypeName()?></span>
		
	<? } ?>
</div>
</td>
</tr>

<? 
$rowNum++;
} ?>

</table>

<input type="hidden" name="aRevertToPagePermissions" id="aRevertToPagePermissions" value="0" />

<div class="ccm-buttons">
<? if ($a->overrideCollectionPermissions()) { ?>
	<a href="javascript:void(0)" onclick="$('#aRevertToPagePermissions').val(1);$('form[name=permissionForm]').get(0).submit()" class="ccm-button-left cancel"><span>Revert to Page Permissions</span></a>
<? } ?>

	<a href="javascript:void(0)" onclick="$('form[name=permissionForm]').get(0).submit()" class="ccm-button-right accept"><span>Update</span></a>
</div>
<div class="ccm-spacer">&nbsp;</div>
</div>

</form>


<? } ?>
