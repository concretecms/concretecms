<?
defined('C5_EXECUTE') or die(_("Access Denied."));

$areaStyle = $a->getAreaCustomStyleRule();

if (is_object($areaStyle)) { ?>
	<div id="<?=$areaStyle->getCustomStyleRuleCSSID(true)?>" class="<?=$areaStyle->getCustomStyleRuleClassName() ?> ccm-area-styles" >
<? } ?>