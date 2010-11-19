<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$c = $b->getBlockCollectionObject();
$class = ($c->isArrangeMode()) ? "ccm-block-arrange" : "ccm-block";

echo ('<div id="b' . $b->getBlockID() . '-' . $a->getAreaID() . '" custom-style="' . $b->getBlockCustomStyleRuleID() . '" class="' . $class . '">'); ?>