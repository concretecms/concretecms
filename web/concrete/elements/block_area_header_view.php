<?
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
$areaStyle = $c->getAreaCustomStyleRule($a);

//global $layoutSpacingActive;
//if($layoutSpacingActive) echo 'TESTING'; 

if (is_object($areaStyle)) { ?>
	<div id="<?=$areaStyle->getCustomStyleRuleCSSID(true)?>" class="<?=$areaStyle->getCustomStyleRuleClassName() ?> ccm-area-styles ccm-area-styles-a<?=$a->getAreaID()?>" >
<? } ?>