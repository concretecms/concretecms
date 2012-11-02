<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$f = $fv->getFile();
$fp = new Permissions($f);
if (!$fp->canEditFileContents()) {
  die(t("Access Denied."));
}
?>

<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/kinetic.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/ccm_app/image.editor.js"></script>
<div class='table'>
  <div class='Editor'>
  </div>
  <div class='controls'>
    &nbsp;
  </div>
</div>


<script>
window.im = $('div.Editor').ImageEditor({src:'<?=$fv->getURL()?>'});
</script>

<style>
div#ccm-dialog-content1 { padding:0; }
div.table {
  height:100%;
  width:100%;
  display:table-layout;
}
div.table > div {
  display:inline-block;
  height:100%;
  float:left;
}
div.Editor {
  width:80%;
}
div.controls {
  width:20%;
  background:white;
  position:relative;
  box-shadow:-5px 0px 25px -5px black;
}
</style>

<?php //print_R($fv->getURL());