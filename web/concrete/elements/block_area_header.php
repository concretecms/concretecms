<? 
defined('C5_EXECUTE') or die("Access Denied.");
//$arHandle = strtolower(preg_replace("/[^0-9A-Za-z]/", "", $a->getAreaHandle()));
// add in a check to see if we're in move mode
$moveModeClass = "";
$c = $a->getAreaCollectionObject();
if ($c->isArrangeMode()) {
	$moveModeClass = "ccm-move-mode";
}
?>
<div id="a<?=$a->getAreaID()?>" handle="<?=$a->getAreaHandle()?>" class="ccm-<? if ($a->isGlobalArea()) { ?>global-<? } ?>area <?=$moveModeClass?>">