<?
$th = $c->getCollectionThemeObject();
$replaceOnUnload = 1;
include("editor_init.php"); // start the advanced editor
?>

<div style="text-align: center" id="ccm-editor-pane">
<textarea id="ccm-content-<?=$b->getBlockID()?>-<?=$a->getAreaID()?>" class="advancedEditor" name="content"><?=$controller->getContentEditMode()?></textarea>
</div>