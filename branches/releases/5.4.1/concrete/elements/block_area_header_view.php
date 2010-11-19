<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
$areaStyle = $c->getAreaCustomStyleRule($a);

//global $layoutSpacingActive;
//if($layoutSpacingActive) echo 'TESTING'; 

if (is_object($areaStyle)) { ?>
	<div id="<?php echo $areaStyle->getCustomStyleRuleCSSID(true)?>" class="<?php echo $areaStyle->getCustomStyleRuleClassName() ?> ccm-area-styles ccm-area-styles-a<?php echo $a->getAreaID()?>" >
<?php  } ?>