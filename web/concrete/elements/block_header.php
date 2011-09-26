<? 
defined('C5_EXECUTE') or die("Access Denied.");
$c = $b->getBlockCollectionObject();
if (!is_object($a)) {
	$a = Area::get($c, $b->getAreaHandle());
}
$class = ($c->isArrangeMode()) ? "ccm-block-arrange" : "ccm-block";
$class .= ($b->isAliasOfMasterCollection() || $b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) ? " ccm-block-alias" : "";

if ($b->getBlockTypeHandle() == BLOCK_HANDLE_STACK_PROXY) {
	$class .= ' ccm-block-stack ';
}
echo ('<div id="b' . $b->getBlockID() . '-' . $a->getAreaID() . '" custom-style="' . $b->getBlockCustomStyleRuleID() . '" class="' . $class . '">'); ?>