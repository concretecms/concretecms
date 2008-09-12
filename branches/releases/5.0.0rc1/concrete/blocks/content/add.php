<?php 
$th = $c->getCollectionThemeObject();
$replaceOnUnload = 1;
include("editor_init.php");
?>

<div style="text-align: center"><textarea id="ccm-content-<?php echo $a->getAreaID()?>" class="advancedEditor" name="content"></textarea></div>