<?
defined('C5_EXECUTE') or die("Access Denied.");
$items = array();
$dsh = Loader::helper('concrete/dashboard/sitemap');
$fp = FilePermissions::getGlobal();

if (Loader::helper('validation/token')->validate('editor')) {

	$obj = new stdClass;
	$obj->coreMenus = array();
	$obj->snippets = array();
	if ($dsh->canRead()) {
		$obj->coreMenus[] = 'insert_page';
	}
	if ($fp->canSearchFileSet()) {
		$obj->coreMenus[] = 'insert_file';
		$obj->coreMenus[] = 'insert_image';
	}
	$u = new User();
	if ($u->isRegistered()) {
		$snippets = SystemContentEditorSnippet::getActiveList();
		foreach($snippets as $sns) {
			$menu = new stdClass;
			$menu->scsHandle = $sns->getSystemContentEditorSnippetHandle();
			$menu->scsName = $sns->getSystemContentEditorSnippetName();
			$obj->snippets[] = $menu;
		}
	}

	print Loader::helper('json')->encode($obj);

}

exit;