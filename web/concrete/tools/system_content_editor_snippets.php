<?
defined('C5_EXECUTE') or die("Access Denied.");
$items = array();
if (Loader::helper('validation/token')->validate()) {
	$snippets = SystemContentEditorSnippet::getActiveList();
	foreach($snippets as $sns) {
		$obj = new stdClass;
		$obj->scsHandle = $sns->getSystemContentEditorSnippetHandle();
		$obj->scsName = $sns->getSystemContentEditorSnippetName();
		$items[] = $obj;
	}
}

print Loader::helper('json')->encode($items);
exit;