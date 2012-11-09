<? 
defined('C5_EXECUTE') or die("Access Denied.");
$c = $b->getBlockCollectionObject();
if (!is_object($a)) {
	$a = Area::get($c, $b->getAreaHandle());
}

$class .= 'ccm-block-edit-inline';
echo ('<div id="b' . $b->getBlockID() . '-' . $a->getAreaID() . '" custom-style="' . $b->getBlockCustomStyleRuleID() . '" class="' . $class . '">'); ?>