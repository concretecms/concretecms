<?
defined('C5_EXECUTE') or die("Access Denied.");
$items = array();
$dsh = Loader::helper('concrete/dashboard/sitemap');
$fp = FilePermissions::getGlobal();

if (Loader::helper('validation/token')->validate('editor')) {

	$obj = new stdClass;
	$obj->snippets = array();
	$u = new User();
	if ($u->isRegistered()) {
		$snippets = \Concrete\Core\Editor\Snippet::getActiveList();
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