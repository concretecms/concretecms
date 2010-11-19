<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID($_REQUEST['cID']); 
$a = Area::get($c, $_GET['arHandle']);
$cp = new Permissions($c);
$ap = new Permissions($a);
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();

if (!$cp->canWrite()) {
	die(t("Access Denied."));
}

$args = array('c'=>$c, 'a' => $a, 'cp' => $cp, 'ap' => $ap, 'token' => $token);

switch($_GET['atask']) {
	case 'add':
		$toolSection = "block_area_add_new";
		$canViewPane = $ap->canAddBlocks();
		break;
	case 'paste':
		$toolSection = "block_area_add_scrapbook";
		$canViewPane = $ap->canAddBlocks();
		break;
	case 'layout':
		$originalLayoutId = (intval($_REQUEST['originalLayoutID'])) ? intval($_REQUEST['originalLayoutID']) : intval($_REQUEST['layoutID']);
		$args['refreshAction'] = REL_DIR_FILES_TOOLS_REQUIRED . '/edit_area_popup?atask=layout&cID=' . $c->getCollectionID() . '&arHandle=' . $a->getAreaHandle() . '&refresh=1&originalLayoutID='.$originalLayoutId.'&cvalID='.$_REQUEST['cvalID'];
		$toolSection = "block_area_layout";
		$canViewPane = $ap->canWrite();
		$args['action'] = $a->getAreaUpdateAction('layout').'&originalLayoutID='.$originalLayoutId.'&cvalID='.intval($_REQUEST['cvalID']);
		break;
	case 'design':
		$toolSection = 'custom_style';
		$args['style'] = $c->getAreaCustomStyleRule($a);
		$args['action'] = $a->getAreaUpdateAction('design');
		$args['refreshAction'] = REL_DIR_FILES_TOOLS_REQUIRED . '/edit_area_popup?atask=design&cID=' . $c->getCollectionID() . '&arHandle=' . $a->getAreaHandle() . '&refresh=1';
		$canViewPane = $ap->canWrite();
		if ($canViewPane) {
			if ($_REQUEST['subtask'] == 'delete_custom_style_preset') {
				$styleToDelete = CustomStylePreset::getByID($_REQUEST['deleteCspID']);
				$styleToDelete->delete(); 
			}
		}		
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

<?php  Loader::element($toolSection, $args);
