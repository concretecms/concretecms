<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$f = $fv->getFile();
$fp = new Permissions($f);
if (!$fp->canEditFileContents()) {
  die(t("Access Denied."));
}

Loader::model('system/image_editor/control_set');
Loader::model('system/image_editor/filter');

$controlsets = SystemImageEditorControlSet::getList();
$filters = SystemImageEditorFilter::getList();

?>
<link rel="stylesheet" href="<?=ASSETS_URL_CSS?>/image_editor/image_editor.css?f=5">
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/image_editor.min.js"></script>
<div class='table ccm-ui'>
  <div class='Editor'></div>
  <div class='controls'>
    <?php
    if (!$controlsets) echo "&nbsp;";
    foreach($controlsets as $controlset) {
      $handle = $controlset->getImageEditorControlSetHandle();
      echo "<link rel='stylesheet' href='/concrete/css/control_sets/{$handle}.css?d=".sha1(rand(1,500000))."'>
          <div class='controlset {$handle}'".
           " data-namespace='{$handle}'".
           " data-src='/concrete/js/image_editor/control_sets/{$handle}.js'>".
              "<h4>".$controlset->getImageEditorControlSetName()."</h4>".
              "<div class='control'>";
                echo Loader::element('control_sets/'.$handle,1);
              echo "</div>".
            "</div>";
    }
    ?>
  </div>
</div>


<script>
$(function(){
  var settings = {src:'<?=$fv->getURL()?>',controlsets:{},filters:{},components:{},debug:true};
  $('div.controlset','div.controls').each(function(){
    settings.controlsets[$(this).attr('data-namespace')] = {
      src:$(this).attr('data-src'),
      element: $(this).children('div.control')
    }
  });
  settings.filters = <?php
    $fnames = array();
    foreach ($filters as $filter) {
      $handle = $filter->getImageEditorFilterHandle();
      $fnames[$handle] = array("src"=>"/concrete/js/image_editor/filters/{$handle}.js","name"=>$filter->getImageEditorFilterName());
    }
    echo Loader::helper('json')->encode($fnames);
  ?>;
  console.log(settings);
  window.im = $('div.Editor').ImageEditor(settings);
})
</script>