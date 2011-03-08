<? 
defined('C5_EXECUTE') or die("Access Denied.");
$c = $b->getBlockCollectionObject();
if (!is_object($a)) {
	$a = Area::get($c, $b->getAreaHandle());
}
$class = ($c->isArrangeMode()) ? "ccm-block-arrange" : "ccm-block";
$class .= ($b->isAliasOfMasterCollection()) ? " ccm-block-alias" : "";
$class .= ($b->isGlobal() || $c->isMasterCollection()) ? " ccm-block-global" : "";

echo ('<div id="b' . $b->getBlockID() . '-' . $a->getAreaID() . '" custom-style="' . $b->getBlockCustomStyleRuleID() . '" class="' . $class . '">'); ?>