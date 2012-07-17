<?
defined('C5_EXECUTE') or die("Access Denied.");

if (!Loader::helper('validation/numbers')->integer($_REQUEST['cID'])) {
	die(t('Access Denied'));
}

$c = Page::getByID($_REQUEST['cID']); 
$a = Area::get($c, $_GET['arHandle']);
$ax = $a;
$cx = $c;
if ($a->isGlobalArea()) {
	$cx = Stack::getByName($a->getAreaHandle());
	$ax = Area::get($cx, STACKS_AREA_NAME);
}

$cp = new Permissions($cx);
$ap = new Permissions($ax);
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();

if (!$cp->canEditPageContents()) {
	die(t("Access Denied."));
}

$args = array('c'=>$c, 'a' => $a, 'cp' => $cp, 'ap' => $ap, 'token' => $token);

Loader::element("dialog_header");

if ($a->isGlobalArea()) {
	echo '<div class="ccm-ui"><div class="alert-message block-message warning">';
	echo t('This is a global area. Content added here will be visible on every page that contains this area.');
	echo('</div></div>');
} 

switch($_GET['atask']) {
	case 'add':
		$toolSection = "block_area_add_new";
		$canViewPane = $ap->canAddBlocks();
		break;
	case 'add_from_stack':
		$toolSection = "block_area_add_stack";
		$canViewPane = $ap->canAddStacks();
		break;
	case 'add_stack_contents':
		$toolSection = "block_area_add_stack_contents";
		$stack = Stack::getByID($_REQUEST['stackID']);
		$canViewPane = false;
		if (is_object($stack)) {
			$stp = new Permissions($stack);
			$canViewPane = ($stp->canRead() && $ap->canAddStacks());
		}
		break;
	case 'paste':
		$toolSection = "block_area_add_scrapbook";
		$canViewPane = $ap->canAddBlocks();
		break;
	case 'layout':
		$originalLayoutId = (intval($_REQUEST['originalLayoutID'])) ? intval($_REQUEST['originalLayoutID']) : intval($_REQUEST['layoutID']);
		$args['refreshAction'] = REL_DIR_FILES_TOOLS_REQUIRED . '/edit_area_popup?atask=layout&cID=' . $c->getCollectionID() . '&arHandle=' . $a->getAreaHandle() . '&refresh=1&originalLayoutID='.$originalLayoutId.'&cvalID='.$_REQUEST['cvalID'];
		$toolSection = "block_area_layout";
		$canViewPane = $ap->canAddLayoutToArea();
		$args['action'] = $a->getAreaUpdateAction('layout').'&originalLayoutID='.$originalLayoutId.'&cvalID='.intval($_REQUEST['cvalID']);
		break;
	case 'design':
		$toolSection = 'custom_style';
		$args['style'] = $c->getAreaCustomStyleRule($a);
		$args['action'] = $a->getAreaUpdateAction('design');
		$args['refreshAction'] = REL_DIR_FILES_TOOLS_REQUIRED . '/edit_area_popup?atask=design&cID=' . $c->getCollectionID() . '&arHandle=' . $a->getAreaHandle() . '&refresh=1';
		$canViewPane = $ap->canEditAreaDesign();
		if ($canViewPane) {
			if ($_REQUEST['subtask'] == 'delete_custom_style_preset') {
				$styleToDelete = CustomStylePreset::getByID($_REQUEST['deleteCspID']);
				$styleToDelete->delete(); 
			}
		}		
		break;
	case 'groups':
		$toolSection = "permission/lists/area";
		$canViewPane = $ap->canEditAreaPermissions();
		break;
	case 'set_advanced_permissions':
		$toolSection = "permission/details/area";
		$canViewPane = $ap->canEditAreaPermissions();
		break;
}

if (!$canViewPane) {
	die(t("Access Denied."));
}

?>

<? Loader::element($toolSection, $args);

 Loader::element("dialog_footer"); ?>