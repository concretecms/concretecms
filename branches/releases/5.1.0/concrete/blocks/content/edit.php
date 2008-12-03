<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$th = $c->getCollectionThemeObject();
$replaceOnUnload = 1;
include("editor_init.php"); // start the advanced editor
?>

<div style="text-align: center" id="ccm-editor-pane">
<textarea id="ccm-content-<?php echo $b->getBlockID()?>-<?php echo $a->getAreaID()?>" class="advancedEditor" name="content"><?php echo $controller->getContentEditMode()?></textarea>
</div>