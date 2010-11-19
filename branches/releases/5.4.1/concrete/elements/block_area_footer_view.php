<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
$areaStyle = $c->getAreaCustomStyleRule($a);

if($areaStyle && $areaStyle->getCustomStyleRuleID() ){ ?></div><?php  } ?>