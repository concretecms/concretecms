<?
defined('C5_EXECUTE') or die(_("Access Denied."));

$b = $obj; 
if( method_exists($b,'getBlockCollectionObject')  ){
	$bc = $b->getBlockCollectionObject(); 
	if(!$b->isGlobal()) {
		$blockStyle = $b->getBlockCustomStyleRule();
	}
} 

if (is_object($blockStyle)) { ?>
	<div id="<?=$blockStyle->getCustomStyleRuleCSSID(true)?>" class="<?=$blockStyle->getCustomStyleRuleClassName() ?> ccm-block-styles" >
<? } ?>