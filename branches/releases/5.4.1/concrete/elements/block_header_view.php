<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$b = $obj; 
if( method_exists($b,'getBlockCollectionObject')  ){
	$bc = $b->getBlockCollectionObject(); 
	$blockStyle = $b->getBlockCustomStyleRule();

} 

if (is_object($blockStyle)) { ?>
	<div id="<?php echo $blockStyle->getCustomStyleRuleCSSID(true)?>" class="<?php echo $blockStyle->getCustomStyleRuleClassName() ?> ccm-block-styles" >
<?php  } ?>