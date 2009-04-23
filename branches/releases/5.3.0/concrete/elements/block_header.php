<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = $b->getBlockCollectionObject();
$class = ($c->isArrangeMode()) ? "ccm-block-arrange" : "ccm-block";

echo ('<div id="b' . $b->getBlockID() . '-' . $a->getAreaID() . '" class="' . $class . '">'); ?>