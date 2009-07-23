<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$replaceOnUnload = 1;
$bt->inc('editor_init.php');
?>

<div style="text-align: center"><textarea id="ccm-content-<?php echo $a->getAreaID()?>" class="advancedEditor" name="content"></textarea></div>