<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByID($_REQUEST['cID']);
$cp = new Permissions($c);
if (!$cp->canWrite()) {
	die(_("Access Denied."));
}

$a = Area::get($c, $_GET['arHandle']);
$ap = new Permissions($a);
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
$btl = $a->getAddBlockTypes($c, $ap );
$blockTypes = $btl->getBlockTypeList();
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');

?>

<script type="text/javascript">
ccm_isRemotelyLoggedIn = '<?php echo UserInfo::isRemotelyLoggedIn()?>';
ccm_remoteUID = <?php echo UserInfo::getRemoteAuthUserId() ?>;
ccm_remoteUName = '<?php echo UserInfo::getRemoteAuthUserName()?>';
ccm_loginInstallSuccessFn = function() { jQuery.fn.dialog.closeTop(); };

function ccm_loginSuccess(jsObj) {
	ccm_isRemotelyLoggedIn = true;
	ccm_remoteUID = jsObj.uID;
	ccm_remoteUName = jsObj.uName;
	jQuery.fn.dialog.closeTop();
	ccm_updateMarketplaceTab();
	ccmAlert.notice('Marketplace Login', ccmi18n.marketplaceLoginSuccessMsg);
}
function ccm_logoutSuccess() {
	ccm_isRemotelyLoggedIn = false;
	ccm_updateMarketplaceTab();
	ccmAlert.notice('Marketplace Logout', ccmi18n.marketplaceLogoutSuccessMsg);
}
function ccm_updateLoginArea() {
	if (ccm_isRemotelyLoggedIn) {
		$("#ccm-marketplace-logged-in").show();
		$("#ccm-marketplace-logged-out").hide();
	} else {
		$("#ccm-marketplace-logged-in").hide();
		$("#ccm-marketplace-logged-out").show();
	}
}
function ccm_updateMarketplaceTab() {
	$("#ccm-add-marketplace-tab div.ccm-block-type-list").html(ccmi18n.marketplaceLoadingMsg);
	$.ajax({
        url: CCM_TOOLS_PATH+'/marketplace/refresh_block',
        type: 'POST',
        success: function(html){
			$("#ccm-add-marketplace-tab div.ccm-block-type-list").html(html);
			ccm_updateLoginArea();
			ccmLoginHelper.bindInstallLinks();
        },
	});
}

$(document).ready(function(){
	ccm_updateMarketplaceTab();
});
</script>

<ul class="ccm-dialog-tabs" id="ccm-area-tabs" style="display:<?php echo ($_REQUEST['addOnly']!=1)?'block':'none'?>">
	<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-add"><?php echo t('Add New')?></a></li>

	<li><a href="javascript:void(0)" id="ccm-add-existing"><?php echo t('Add From Scrapbook')?></a></li>
	
	<?php  if(ENABLE_MARKETPLACE_SUPPORT){ ?>
		<li><a href="javascript:void(0)" id="ccm-add-marketplace"><?php echo t('Add From Marketplace')?></a></li>
	<?php  } ?>
	
	<?php  if (PERMISSIONS_MODEL != 'simple' && $cp->canAdminPage()) { ?><li><a href="javascript:void(0)" id="ccm-permissions"><?php echo t('Permissions')?></a></li><?php  } ?>
</ul>

<div id="ccm-add-tab">
	<h1><?php echo t('Add New Block')?></h1>
	<div id="ccm-block-type-list">
	<?php  if (count($blockTypes) > 0) { 
		foreach($blockTypes as $bt) { 
			$btIcon = $ci->getBlockTypeIconURL($bt);
			?>	
			<div class="ccm-block-type">
				<a class="ccm-block-type-help" href="javascript:ccm_showBlockTypeDescription(<?php echo $bt->getBlockTypeID()?>)" title="<?php echo t('Learn more about this block type.')?>" id="ccm-bt-help-trigger<?php echo $bt->getBlockTypeID()?>"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/help.png" width="14" height="14" /></a>
				<a class="dialog-launch ccm-block-type-inner" dialog-modal="true" dialog-width="<?php echo $bt->getBlockTypeInterfaceWidth()?>" dialog-height="<?php echo $bt->getBlockTypeInterfaceHeight()?>" style="background-image: url(<?php echo $btIcon?>)" dialog-title="<?php echo t('Add')?> <?php echo $bt->getBlockTypeName()?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/add_block_popup.php?cID=<?php echo $c->getCollectionID()?>&btID=<?php echo $bt->getBlockTypeID()?>&arHandle=<?php echo $a->getAreaHandle()?>"><?php echo $bt->getBlockTypeName()?></a>
				<div class="ccm-block-type-description"  id="ccm-bt-help<?php echo $bt->getBlockTypeID()?>"><?php echo $bt->getBlockTypeDescription()?></div>
			</div>
		<?php  }
	} else { ?>
		<p><?php echo t('No block types can be added to this area.')?></p>
	<?php  } ?>
	</div>
</div>

<?php  if(ENABLE_MARKETPLACE_SUPPORT){ ?>
<div id="ccm-add-marketplace-tab" style="display: none">
	<h1><?php echo t('Add From Marketplace')?></h1>
	<div class="ccm-block-type-list">
		<p><?php echo t('Unable to connect to the Concrete5 Marketplace.')?></p>
	</div>
</div>
<?php  } ?>

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

ccmPrepareScrapbookItemsDelete=function(){
	$("a.ccm-scrapbook-delete").click(function() {
		var id=$(this).attr('id');
		var arHandle=$(this).attr('arHandle');
		var qStr='&ptask=delete_content&arHandle='+encodeURIComponent(arHandle)+'<?php echo $token?>'; 
		
		if( id.indexOf('bID')>-1 ){
			if(!confirm('<?php echo t('Are you sure you want to delete this block?').'\n'.t('(All page instances will also be removed)') ?>'))
				return false;
			var bID = id.substring(13);
			qStr='bID=' + bID + qStr;
		}else{
			var pcID = id.substring(2);
			qStr='pcID=' + pcID + qStr;
		}  
		$.ajax({
			type: 'POST',
			url: CCM_DISPATCHER_FILENAME,
			data: qStr,
			success: function(msg) {
				if(pcID) $("#ccm-pc-" + pcID).fadeOut();
				if(bID)  $("#ccm-scrapbook-list-item-" + bID).fadeOut(); 
			}
		}); 
	});
}

$(function(){ ccmPrepareScrapbookItemsDelete(); });
	
ccmChangeDisplayedScrapbook = function(sel){  
	var scrapbook=$(sel).val(); 
	if(!scrapbook) return false;
	$('#ccm-scrapbookListsWrap').html("<div style='padding:16px;'><?php echo t('Loading...')?></div>");
	$.ajax({
	type: 'POST',
	url: CCM_TOOLS_PATH+"/edit_area_scrapbook_refresh.php",
	data: 'cID=<?php echo intval($_REQUEST['cID'])?>&arHandle=<?php echo urlencode($_REQUEST['arHandle'])?>&scrapbookName='+scrapbook+'&<?php echo $token?>',
	success: function(resp) { 
		$('#ccm-scrapbookListsWrap').html(resp);
		ccmPrepareScrapbookItemsDelete(); 
	}});		
	return false;
}
</script>

<div id="ccm-add-existing-tab" style="display:none">

	<?php  
	$u = new User();
	$scrapbookHelper=Loader::helper('concrete/scrapbook'); 
	$scrapBookAreasData = $scrapbookHelper->getAvailableScrapbooks(); 
	$scrapbookName=$_SESSION['ccmLastViewedScrapbook']; 
	?>	
	<select name="scrapbookName" onchange="ccmChangeDisplayedScrapbook(this)" style=" float:right; margin-top:6px;"> 
		<option value="userScrapbook">
			<?php echo ucfirst($u->getUserName()) ?><?php echo t("'s Scrapbook") ?> 
		</option>
		<?php  foreach($scrapBookAreasData as $scrapBookAreaData){ ?>
			<option value="<?php echo addslashes($scrapBookAreaData['arHandle'])?>" <?php echo ($scrapbookName==$scrapBookAreaData['arHandle'])?'selected':''?>>
				<?php echo $scrapBookAreaData['arHandle'] ?>
			</option>
		<?php  } ?>
	</select> 	

	<h1><?php echo t('Add From Scrapbook')?></h1>		
		
	<div id="ccm-scrapbookListsWrap">
	<?php  Loader::element('scrapbook_lists', array( 'c'=>$c, 'a'=>$a, 'scrapbookName'=>$scrapbookName, 'token'=>$token ) );  ?>
	</div>

</div>



<div id="ccm-permissions-tab" style="display: none"> 
	<h1><?php echo t('Set Area Permissions')?></h1>

	<?php 
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
				row1.id = "_row_uID_" + uID;
				row2 = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
				row3 = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?    
				
				row1.className = rowValue.replace(":","_");
				row2.className = rowValue.replace(":","_");
				row3.className = rowValue.replace(":","_");
				
				row1Cell = document.createElement("TH");
				row1Cell.innerHTML = '<a href="javascript:removePermissionRow(\'' + rowValue.replace(':','_') + '\',\'' + rowText + '\')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12" style="float: right" ></a>' + rowText;
				row1Cell.colSpan = 7;
				row1Cell.className =  'ccm-permissions-header';
				row1.appendChild(row1Cell);
				
				row2Cell1 = row2.insertCell(0);
				row2Cell2 = row2.insertCell(1);
				row2Cell3 = row2.insertCell(2);
				row2Cell4 = row2.insertCell(3);
				row2Cell5 = row2.insertCell(4);
				row2Cell6 = row2.insertCell(5);
				row2Cell7 = row2.insertCell(6);
				
				row2Cell1.vAlign = 'top';
				row2Cell2.vAlign = 'top';
				row2Cell3.vAlign = 'top';
				row2Cell4.vAlign = 'top';
				row2Cell5.vAlign = 'top';
				row2Cell6.vAlign = 'top';
				row2Cell7.width = '100%';
				
				row2Cell1.innerHTML = '<div style="text-align: center"><strong><?php echo t('Read')?></strong></div>';
				row2Cell2.innerHTML = '<input type="checkbox" name="areaRead[]" value="' + rowValue + '">';
				row2Cell3.innerHTML = '<div style="width: 54px; text-align: right"><strong><?php echo t('Write')?></strong></div>';
				row2Cell4.innerHTML = '<input type="checkbox" name="areaEdit[]" value="' + rowValue + '" />';
				row2Cell5.innerHTML = '<div style="width: 54px; text-align: right"><strong><?php echo t('Delete')?></strong></div>';
				row2Cell6.innerHTML = '<input type="checkbox" name="areaDelete[]" value="' + rowValue + '" />';
				row2Cell7.innerHTML = '<div style="width: 225px">&nbsp;</div>';
				
				row3Cell1 = row3.insertCell(0);
				row3Cell1.vAlign = 'top';
				row3Cell1.innerHTML = '<div style="text-align: center"><strong><?php echo t('Add')?></strong></div>';
				row3Cell2 = row3.insertCell(1);
				row3Cell2.colSpan = 7;
				row3Cell2.vAlign = 'top';
				row3Cell2.width = '100%';
				row3Cell2.innerHTML = '<div style="width: 460px;">';
				<?php  foreach ($btArray as $bt) { ?>
					row3Cell2.innerHTML += '<div style="white-space: nowrap; float: left; width: 80px; margin-right: 20px"><input type="checkbox" name="areaAddBlockType[<?php echo $bt->getBlockTypeID()?>][]" value="' + rowValue + '" />&nbsp;<?php echo $bt->getBlockTypeName()?></div>';
				<?php  } ?>		
				row3Cell2.innerHTML += '</div>';
			}
			
			function ccm_triggerSelectGroup(gID, gName) {
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
				
				row1Cell = document.createElement("TH");
				row1Cell.innerHTML = '<a href="javascript:removePermissionRow(\'' + rowValue.replace(':','_') + '\',\'' + rowText + '\')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12" style="float: right" ></a>' + rowText;
				row1Cell.colSpan = 7;
				row1Cell.className =  'ccm-permissions-header';
				row1.appendChild(row1Cell);
				
				row2Cell1 = row2.insertCell(0);
				row2Cell2 = row2.insertCell(1);
				row2Cell3 = row2.insertCell(2);
				row2Cell4 = row2.insertCell(3);
				row2Cell5 = row2.insertCell(4);
				row2Cell6 = row2.insertCell(5);
				row2Cell7 = row2.insertCell(6);
				
				row2Cell1.vAlign = 'top';
				row2Cell2.vAlign = 'top';
				row2Cell3.vAlign = 'top';
				row2Cell4.vAlign = 'top';
				row2Cell5.vAlign = 'top';
				row2Cell6.vAlign = 'top';
				row2Cell7.width = '100%';
				
				row2Cell1.innerHTML = '<div style="text-align: center"><strong><?php echo t('Read')?></strong></div>';
				row2Cell2.innerHTML = '<input type="checkbox" name="areaRead[]" value="' + rowValue + '">';
				row2Cell3.innerHTML = '<div style="width: 54px; text-align: right"><strong><?php echo t('Write')?></strong></div>';
				row2Cell4.innerHTML = '<input type="checkbox" name="areaEdit[]" value="' + rowValue + '" />';
				row2Cell5.innerHTML = '<div style="width: 54px; text-align: right"><strong><?php echo t('Delete')?></strong></div>';
				row2Cell6.innerHTML = '<input type="checkbox" name="areaDelete[]" value="' + rowValue + '" />';
				row2Cell7.innerHTML = '<div style="width: 225px">&nbsp;</div>';
				
				row3Cell1 = row3.insertCell(0);
				row3Cell1.vAlign = 'top';
				row3Cell1.innerHTML = '<div style="text-align: center"><strong><?php echo t('Add')?></strong></div>';
				row3Cell2 = row3.insertCell(1);
				row3Cell2.colSpan = 7;
				row3Cell2.vAlign = 'top';
				row3Cell2.width = '100%';
				row3Cell2.innerHTML = '<div style="width: 460px;">';
				<?php  foreach ($btArray as $bt) { ?>
					row3Cell2.innerHTML += '<div style="white-space: nowrap; float: left; width: 80px; margin-right: 20px"><input type="checkbox" name="areaAddBlockType[<?php echo $bt->getBlockTypeID()?>][]" value="' + rowValue + '" />&nbsp;<?php echo $bt->getBlockTypeName()?></div>';
				<?php  } ?>		
				row3Cell2.innerHTML += '</div>';
				
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

<form method="post" name="permissionForm" action="<?php echo $a->getAreaUpdateAction()?>">
	<?php  
	if ($a->getAreaCollectionInheritID() != $c->getCollectionID() && $a->getAreaCollectionInheritID() > 0) {
		$pc = $c->getPermissionsCollectionObject(); 
		$areac = Page::getByID($a->getAreaCollectionInheritID());
		?>
		
		<p>
		<?php echo t("The following area permissions are inherited from an area set on ")?>
		<a href="<?php echo DIR_REL?>/index.php?cID=<?php echo $areac->getCollectionID()?>"><?php echo $areac->getCollectionName()?></a>. 
		<?php echo t("To change them everywhere, edit this area on that page. To override them here and on all sub-pages, edit below.")?>
		</p>

<?php  	} else if (!$a->overrideCollectionPermissions()) { ?>

	<?php echo t("The following area permissions are inherited from the page's permissions. To override them, edit below.")?>

<?php  } else { ?>

	<span class="ccm-important">
		<?php echo t("Permissions for this area currently override those of the page. To revert to the page's permissions, click <strong>revert to page permissions</strong> below.")?>
		<br/><br/>
	</span>

<?php  } ?>

	<div class="ccm-buttons" style="margin-bottom: 10px"> 
		<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector.php?cID=<?php echo $_REQUEST['cID']?>" dialog-width="600" dialog-title="<?php echo t('Choose User/Group')?>"  dialog-height="400" class="dialog-launch ccm-button-right"><span><em class="ccm-button-add"><?php echo t('Add Group or User')?></em></span></a>
	</div>
	<div class="ccm-spacer">&nbsp;</div><br/>

	<table id="ccmPermissionsTable" border="0" cellspacing="0" cellpadding="0" class="ccm-grid" style="width: 100%">
		<?php  
		
		$rowNum = 1;
		foreach ($gArray as $g) { 
			$displayRow = false;
			$display = (($g->getGroupID() == GUEST_GROUP_ID || $g->getGroupID() == REGISTERED_GROUP_ID) || $g->canRead() || $g->canWrite() || $g->canAddBlocks() || $g->canDeleteBlock()) 
			? true : false;
			
			if ($display) { ?>
	
				<tr class="gID_<?php echo $g->getGroupID()?>" id="_row_gID_<?php echo $g->getGroupID()?>">
				<th colspan="7" style="text-align: left; white-space: nowrap"><?php  if ($g->getGroupID() != GUEST_GROUP_ID && $g->getGroupID() != REGISTERED_GROUP_ID) { ?>    
							<a href="javascript:removePermissionRow('gID_<?php echo $g->getGroupID()?>', '<?php echo $g->getGroupName()?>')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12" style="float: right" ></a>
						<?php  } ?>
						<?php echo $g->getGroupName()?></th>		
				</tr>
				<tr class="gID_<?php echo $g->getGroupID()?>">
				<td valign="top" style="text-align: center"><strong><?php echo t('Read')?></strong></td>
				<td valign="top" ><input type="checkbox" name="areaRead[]" value="gID:<?php echo $g->getGroupID()?>"<?php  if ($g->canRead()) { ?> checked<?php  } ?>></td>
				<td><div style="width: 54px; text-align: right"><strong><?php echo t('Write')?></strong></div></td>
				<td><input type="checkbox" name="areaEdit[]" value="gID:<?php echo $g->getGroupID()?>"<?php  if ($g->canWrite()) { ?> checked<?php  } ?>></td>
				<td><div style="width: 54px; text-align: right"><strong><?php echo t('Delete')?></strong></div></td>
				<td><input type="checkbox" name="areaDelete[]" value="gID:<?php echo $g->getGroupID()?>"<?php  if ($g->canDeleteBlock()) { ?> checked<?php  } ?>></td>
				<td valign="top" width="100%"><div style="width: 225px">&nbsp;</div></td>
				</tr>
				<tr class="gID_<?php echo $g->getGroupID()?>">
				<td valign="top"  style="text-align: center"><strong><?php echo t('Add')?></strong></td>
				<td colspan="6" width="100%">
				<div style="width: 460px;">
					<?php  foreach ($btArray as $bt) { ?>
						<span style="white-space: nowrap; float: left; width: 80px; margin-right: 20px"><input type="checkbox" name="areaAddBlockType[<?php echo $bt->getBlockTypeID()?>][]" value="gID:<?php echo $g->getGroupID()?>"<?php  if ($bt->canAddBlock($g)) { ?> checked<?php  } ?>>&nbsp;<?php echo $bt->getBlockTypeName()?></span>		
					<?php  } ?>
				</div>
				</td>
				</tr>

			<?php  
				$rowNum++;
			} ?>
	<?php   }
		
		foreach ($ulArray as $ui) { ?>
	
			<tr id="_row_uID_<?php echo $ui->getUserID()?>" class="uID_<?php echo $ui->getUserID()?> no-bg">
				<th colspan="7" style="text-align: left; white-space: nowrap">
					<a href="javascript:removePermissionRow('uID_<?php echo $ui->getUserID()?>')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12" style="float: right"></a>
					<?php echo $ui->getUserName()?>
				</th>		
			</tr>
			<tr class="uID_<?php echo $ui->getUserID()?>" >
				<td valign="top" style="text-align: center"><strong><?php echo t('Read')?></strong></td>
				<td valign="top" ><input type="checkbox" name="areaRead[]" value="uID:<?php echo $ui->getUserID()?>"<?php  if ($ui->canRead()) { ?> checked<?php  } ?>></td>
				<td><div style="width: 54px; text-align: right"><strong><?php echo t('Write')?></strong></div></td>
				<td><input type="checkbox" name="areaEdit[]" value="uID:<?php echo $ui->getUserID()?>"<?php  if ($ui->canWrite()) { ?> checked<?php  } ?>></td>
				<td><div style="width: 54px; text-align: right"><strong><?php echo t('Delete')?></strong></div></td>
				<td><input type="checkbox" name="areaDelete[]" value="uID:<?php echo $ui->getUserID()?>"<?php  if ($ui->canDeleteBlock()) { ?> checked<?php  } ?>></td>
				<td valign="top" width="100%"><div style="width: 225px">&nbsp;</div></td>
			</tr>
			<tr class="uID_<?php echo $ui->getUserID()?>" >
				<td valign="top"  style="text-align: center"><strong><?php echo t('Add')?></strong></td>
				<td colspan="6" width="100%">
				<div style="width: 460px;">
					<?php  foreach ($btArray as $bt) { ?>
						<span style="white-space: nowrap; float: left; width: 80px; margin-right: 20px"><input type="checkbox" name="areaAddBlockType[<?php echo $bt->getBlockTypeID()?>][]" value="uID:<?php echo $ui->getUserID()?>"<?php  if ($bt->canAddBlock($ui)) { ?> checked<?php  } ?>>&nbsp;<?php echo $bt->getBlockTypeName()?></span>
					<?php  } ?>
				</div>
				</td>
			</tr>
		
			<?php  
			$rowNum++;
		} ?>
	
	</table>
	
	<input type="hidden" name="aRevertToPagePermissions" id="aRevertToPagePermissions" value="0" />

	<div class="ccm-buttons">
	<?php  if ($a->overrideCollectionPermissions()) { ?>
		<a href="javascript:void(0)" onclick="$('#aRevertToPagePermissions').val(1);$('form[name=permissionForm]').get(0).submit()" class="ccm-button-left cancel"><span><?php echo t('Revert to Page Permissions')?></span></a>
	<?php  } ?>
		<a href="javascript:void(0)" onclick="$('form[name=permissionForm]').get(0).submit()" class="ccm-button-right accept"><span><?php echo t('Update')?></span></a>
	</div>
	<div class="ccm-spacer">&nbsp;</div> 

</form>

<?php  } ?>
