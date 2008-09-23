<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$th = $c->getCollectionThemeObject();
$replaceOnUnload = 1;
include("editor_init.php");
?>

<div style="text-align: center"><textarea id="ccm-content-<?=$a->getAreaID()?>" class="advancedEditor" name="content"></textarea></div>