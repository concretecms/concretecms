<?

defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getByID($_GET['cID'], 'RECENT');
$cp = new Permissions($c);
$canViewPane = false;

$additionalArgs = array();

switch($_GET['ctask']) {
	case 'edit_metadata':
		$toolSection = "collection_metadata";
		$canViewPane = $cp->canWrite();
		break;
	case 'edit_speed_settings':
		$toolSection = "collection_speed_settings";
		$canViewPane = $cp->canAdminPage();
		break;
	case 'edit_permissions':
		if (PERMISSIONS_MODEL == 'simple') {
			$toolSection = 'collection_permissions_simple';
		} else {
			$toolSection = "collection_permissions";
		}
		$canViewPane = $cp->canAdminPage();
		break;
	case 'edit_permissions_composer':
		$toolSection = "collection_permissions";
		$canViewPane = $cp->canAdminPage();
		$additionalArgs['isComposer'] = true;
		break;
	case 'mcd':
		$toolSection = "collection_mcd";
		$canViewPane = $cp->canWrite();
		$divID = "ccm-collection-mcd";
		break;
	case 'delete':
		$toolSection = "collection_delete";
		$canViewPane = $cp->canDeleteCollection();
		break;
	case 'set_theme':
		$toolSection = "collection_theme";
		$divID = 'ccm-edit-collection-design';
		$canViewPane = $cp->canWrite();
		break;
	case 'add':
		$toolSection = "collection_add";
		$divID = 'ccm-edit-collection-design';
		$canViewPane = $cp->canAddSubContent();
		if ($_REQUEST['ctID']) {
			$ct = CollectionType::getByID($_REQUEST['ctID']);
			if (!is_object($ct)) {
				$canViewPane = false;
			} else {
				$canViewPane = $cp->canAddSubCollection($ct);
			}
		}
		break;
	case 'add_external':
		$toolSection = "collection_add_external";
		$divID = 'ccm-edit-collection-external';
		$canViewPane = $cp->canWrite();
		break;
	case 'edit_external':
		$toolSection = "collection_edit_external";
		$divID = 'ccm-edit-collection-external';
		$cparent = Page::getByID($c->getCollectionParentID(), "RECENT");
		$cparentP = new Permissions($cparent);
		$canViewPane = $cparentP->canWrite();
		break;
}
if ($toolSection == "collection_permissions" && !$cp->canAdminPage()) {
	$toolSection = "collection_metadata";
}
if (!isset($divID)) {
	$divID = 'ccm-edit-collection';
}

if (!$canViewPane) {
	die(t("Access Denied."));
}

?>

<? if ($_REQUEST['toppane'] == 1) {
	Loader::element('pane_header', array('c'=>$c));
}
?>

<div id="<?=$divID?>">

<? if (!$_GET['close']) {

	if (!$c->isEditMode() && ($_GET['ctask'] != 'add')) {
		// first, we attempt to check the user in as editing the collection
		$u = new User();
		if ($u->isRegistered()) {
			$u->loadCollectionEdit($c);
		}
	}
	
	if (($c->isEditMode() || ($_GET['ctask'] == 'add')) && $toolSection) {
		$args = array(
			'c' => $c,
			'cp' => $cp,
			'ct' => $ct
		);
		$args = array_merge($args, $additionalArgs);
		Loader::element($toolSection, $args);
	} else {
		$error = t("Someone has already checked out this page for editing.");
	}

}

if ($error) {
	echo($error);
} ?>
<div class="ccm-spacer">&nbsp;</div>

</div>

<? if ($_REQUEST['toppane'] == 1) { ?>
	<? Loader::element('pane_footer', array('c'=>$c)); ?>
<? } ?>