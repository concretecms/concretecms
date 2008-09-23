<?

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('collection_types');

$error = "An unspecified error has occurred.";

if (isset($_REQUEST['origCID'] ) && is_numeric($_REQUEST['origCID'])) {
	$oc = Page::getByID($_REQUEST['origCID']);
}
if (isset($_REQUEST['destCID'] ) && is_numeric($_REQUEST['destCID'])) {
	$dc = Page::getByID($_REQUEST['destCID']);
}

$json = array();
$json['error'] = false;
$json['message'] = false;

if (is_object($oc) && is_object($dc)) {
	$ocp = new Permissions($oc);
	$dcp = new Permissions($dc);
	$ct = CollectionType::getByID($dc->getCollectionTypeID());
	if (!$ocp->canRead()) {
		$error = "You cannot view the source page.";
	} else if (!$dcp->canAddSubContent($ct)) {
		$error = "You do not have sufficient privileges to add this type of page to this destination.";
	} else if (!$oc->canMoveCopyTo($dc)) {
		$error = "You may not move/copy/alias the chosen page to that location.";
	} else {
		$error = false;
	}
}

if (!$error) {
	if ($_REQUEST['ctask']) {
		switch($_REQUEST['ctask']) {
			case "ALIAS":
				$ncID = $oc->addCollectionAlias($dc);
				$successMessage = '"' . $oc->getCollectionName() . '" was successfully aliased beneath "' . $dc->getCollectionName() . '"';
				$newCID = $ncID;
				break;
			case "COPY":
				if ($_REQUEST['copyAll'] && $dcp->canAdminPage()) {
					$nc2 = $oc->duplicateAll($dc); // new collection is passed back
					if (is_object($nc2)) {
						$successMessage = '"' . $oc->getCollectionName() . '" and all its children were successfully copied beneath "' . $dc->getCollectionName() . '"';
					}
				} else {
					$nc2 = $oc->duplicate($dc);
					if (is_object($nc2)) {
						$successMessage = '"' . $oc->getCollectionName() . '" was successfully copied beneath "' . $dc->getCollectionName() . '"';
					}
				}
				if (!is_object($nc2)) {
					$error = "An error occurred while attempting the copy operation.";
				} else {
					$newCID = $nc2->getCollectionID();
				}
				break;
			case "MOVE":
				if ($dcp->canApproveCollection() && $ocp->canApproveCollection()) {
					$nc2 = $oc->move($dc);
					$successMessage = '"' . $oc->getCollectionName() . '" was moved beneath "' . $dc->getCollectionName() . '"';
				} else {
					$oc->markPendingAction('MOVE', $dc);
					$successMessage = 'Your request to move "' . $oc->getCollectionName() . '" beneath "' . $dc->getCollectionName() . '" has been stored. Someone with approval rights will have to activate the change.';
				}
				$newCID = $oc->getCollectionID();
				break;
		}
	}
}


if ($successMessage) {
	$json['error'] = false;
	$json['message'] = $successMessage;
	$json['cID'] = $newCID;
	print json_encode($json);
	exit;
} else if ($error) {
	if ($_REQUEST['ctask']) {
		$json['error'] = true;
		$json['message'] = $error;
		print json_encode($json);
	} else {
		print '<div class="error">' . $error . '</div>';
	}
	exit;
}

?>

<h2>You dragged "<?=$oc->getCollectionName()?>" onto "<?=$dc->getCollectionName()?>." What do you wish to do?</h2><br/>
	<form>

		<input type="hidden" name="origCID" id="origCID" value="<?=$_REQUEST['origCID']?>" />
		<input type="hidden" name="destParentID" id="destParentID" value="<?=$dc->getCollectionParentID()?>" />
		<input type="hidden" name="destCID" id="destCID" value="<?=$_REQUEST['destCID']?>" />

		<input type="radio" checked style="vertical-align: middle" id="ctaskMove" name="ctask" value="MOVE" onclick="toggleMove()" />
		<strong>Move</strong> "<?=$oc->getCollectionName()?>" beneath "<?=$dc->getCollectionName()?>"
		<br/><br/>
		
		<? if ($oc->getCollectionPointerID() < 1) { ?>
		<input type="radio" style="vertical-align: middle" id="ctaskAlias" name="ctask" value="ALIAS" onclick="toggleAlias()" />
		<strong>Alias</strong> "<?=$oc->getCollectionName()?>" beneath "<?=$dc->getCollectionName()?>" - The page will appear in both locations; all edits to the original will be reflected in the alias.
		<br/><br/>
		<? } ?>
		
		<input type="radio" style="vertical-align: middle" id="ctaskCopy" name="ctask" value="COPY" onclick="toggleCopy()" />
		<strong>Copy</strong> "<?=$oc->getCollectionName()?>" beneath "<?=$dc->getCollectionName()?>"
		<div style="margin: 4px 0px 0px 20px">
		<? if ($ocp->canAdminPage() && $oc->getCollectionPointerID() < 1) { ?>
			<input type="radio" id="copyThisPage" name="copyAll" value="0" style="vertical-align: middle" disabled /> Copy this page.<br/>
			<input type="radio" id="copyChildren" name="copyAll" value="1" style="vertical-align: middle" disabled /> Copy this page + children.
		<? } else { ?>
			Your copy operation will only affect the current page - not any children.
		<? } ?>
		</div>
		
		<br/>
	
	<div class="ccm-buttons">
	<? if ($_REQUEST['sitemap_mode'] == 'move_copy_delete') { ?>
		<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_overlay.php?reveal=<?=$oc->getCollectionID()?>&sitemap_mode=<?=$_REQUEST['sitemap_mode']?>" id="ccm-exit-drag-request" title="Choose Page" class="ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>
		<script type="text/javascript">$(function() {tb_init('#ccm-exit-drag-request');})</script>


	<? } else { ?>
		<a href="javascript:void(0)" onclick="showBranch(<?=$oc->getCollectionID()?>);$.fn.dialog.closeTop()" class="ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>
	<? } ?>
	<a href="javascript:void(0)" onclick="moveCopyAliasNode(<? if ($_REQUEST['sitemap_mode'] == 'move_copy_delete') { ?>true<? } ?>)" class="ccm-button-right accept"><span>Go</span></a>
	</div>
	
	<div class="ccm-spacer">&nbsp;</div>
	</form>
