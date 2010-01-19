<?
defined('C5_EXECUTE') or die(_("Access Denied."));
//$replaceOnUnload = 1;
$bt->inc('editor_init.php');
?>

<div style="text-align: center" id="ccm-editor-pane">
<textarea id="ccm-content-<?=$b->getBlockID()?>-<?=$a->getAreaID()?>" class="ccm-advanced-editor" name="content"><?=$controller->getContentEditMode()?></textarea>
</div>