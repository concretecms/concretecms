<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByID($_REQUEST['cID']);
$a = Area::get($c, $_GET['arHandle']);
$cp = new Permissions($c);
$ap = new Permissions($a);
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();

if (!$cp->canWrite()) {
	die(t("Access Denied."));
}

switch($_GET['atask']) {
	case 'add':
		$toolSection = "block_area_add_new";
		$canViewPane = $ap->canAddBlocks();
		break;
	case 'paste':
		$toolSection = "block_area_add_scrapbook";
		$canViewPane = $ap->canAddBlocks();
		break;
	case 'design':
		$toolSection = "block_area_design";
		$canViewPane = $ap->canWrite();
		break;
	case 'layout':
		$toolSection = "block_area_layout";
		$canViewPane = $ap->canWrite();
		break;
	case 'groups':
		$toolSection = "block_area_groups";
		$canViewPane = $cp->canAdmin();
		break;
}

if (!$canViewPane) {
	die(_("Access Denied."));
}

?>

<? Loader::element($toolSection, array('c'=>$c, 'a' => $a, 'cp' => $cp, 'ap' => $ap, 'token' => $token));
