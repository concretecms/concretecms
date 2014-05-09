<?
defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Http\ResponseAssetGroup;
use \Concrete\Core\ImageEditor\ControlSet as SystemImageEditorControlSet;
use \Concrete\Core\ImageEditor\Filter as SystemImageEditorFilter;
use \Concrete\Core\ImageEditor\Component as SystemImageEditorComponent;

$editorid = substr(sha1(time()),0,5); // Just enough entropy.

$u = new User();
$form = Loader::helper('form');
$f = $fv->getFile();
$fp = new Permissions($f);
if (!$fp->canEditFileContents()) {
  die(t("Access Denied."));
}

$req = ResponseAssetGroup::get();
$req->requireAsset('core/imageeditor');

$controlsets = SystemImageEditorControlSet::getList();
$components = SystemImageEditorComponent::getList();
$filters = SystemImageEditorFilter::getList();

?>
<link rel="stylesheet" href="<?=ASSETS_URL_CSS?>/ccm.image_editor.css?f=<?=sha1(filemtime(".".ASSETS_URL_CSS."/ccm.image_editor.css"))?>">
<div class='table ccm-ui'>
  <div class='editorcontainer'>
    <div id='<?=$editorid?>' class='Editor'></div>
    <div class='bottomBar'></div>
  </div>
  <div class='controls'>
    <div class='controlscontainer'>
      <?php/*<ul class='nav nav-tabs'>
        <li class='active'><a href='#'>Edit</a></li>
        <li><a href='#'>Add</a></li>
      </ul>*/?>
      <div class='editorcontrols'>
        <div class='control-sets'>
          <?php
          if (!$controlsets) echo "&nbsp;";
          foreach($controlsets as $controlset) {
            $handle = $controlset->getImageEditorControlSetHandle();
            echo "<link rel='stylesheet' href='/concrete/css/image_editor/control_sets/{$handle}.css?d=".sha1(rand(1,500000))."'>
                <div class='controlset {$handle}'".
                 " data-namespace='{$handle}'".
                 " data-src='/concrete/js/image_editor/control_sets/{$handle}.js'>".
                    "<h4>".$controlset->getImageEditorControlSetDisplayName()."</h4>".
                    "<div class='control'><div class='contents'>";
                      echo Loader::element('image_editor/control_sets/'.$handle,array('editorid'=>$editorid));
                    echo "</div></div>".
                    "<div class='border'></div>".
                  "</div>";
          }
          ?>
        </div>
        <?php/*<div class='components'>
          <?php
          if (!$components) echo "&nbsp;";
          foreach($components as $component) {
            $handle = $component->getImageEditorComponentHandle();
            echo "<link rel='stylesheet' href='/concrete/css/image_editor/components/{$handle}.css?d=".sha1(rand(1,500000))."'>
                <div class='component {$handle}'".
                 " data-namespace='{$handle}'".
                 " data-src='/concrete/js/image_editor/components/{$handle}.js'>".
                    "<h4>".$component->getImageEditorComponentDisplayName()."</h4>".
                    "<div class='control'><div class='contents'>";
                      echo Loader::element('image_editor/components/'.$handle,array('editorid'=>$editorid));
                    echo "</div></div>".
                    "<div class='border'></div>".
                  "</div>";
          }
          ?>
        </div>*/?>
      </div>
      <div class='save'>
        <button class='cancel btn'>Cancel</button>
        <button class='save btn pull-right btn-primary'>Save</button>
      </div>
    </div>
  </div>
</div>

<script>
$(function(){
  _.defer(function(){
    var settings = {src:'<?=$fv->getURL()?>',fID:<?=$fv->fID?>,controlsets:{},filters:{},components:{},debug:true};
    $('div.controlset','div.controls').each(function(){
      settings.controlsets[$(this).attr('data-namespace')] = {
        src:$(this).attr('data-src'),
        element: $(this).children('div.control').children('div.contents')
      }
    });
    $('div.component','div.controls').each(function(){
      settings.components[$(this).attr('data-namespace')] = {
        src:$(this).attr('data-src'),
        element: $(this).children('div.control').children('div.contents')
      }
    });
    settings.filters = <?php
      $fnames = array();
      foreach ($filters as $filter) {
        $handle = $filter->getImageEditorFilterHandle();
        $fnames[$handle] = array("src"=>"/concrete/js/image_editor/filters/{$handle}.js","name"=>$filter->getImageEditorFilterDisplayName('text'));
      }
      echo Loader::helper('json')->encode($fnames);
    ?>;
    var editor = $('div#<?=$editorid?>.Editor');
    window.im = editor.closest('.ui-dialog-content').css('padding',0).end().ImageEditor(settings);
  });
});
</script>
