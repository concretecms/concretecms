<?
defined('C5_EXECUTE') or die("Access Denied.");
//$replaceOnUnload = 1;
$bt->inc('editor_init.php');
?>

<div style="text-align: center"><textarea id="ccm-content-<?=$a->getAreaID()?>" class="advancedEditor ccm-advanced-editor" name="content" style="width: 580px; height: 380px"></textarea></div>