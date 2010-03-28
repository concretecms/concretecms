<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$c = Page::getCurrentPage();
$areaStyle = $c->getAreaCustomStyleRule($a);

if($areaStyle && $areaStyle->getCustomStyleRuleID() ){ ?></div><?php  } ?>