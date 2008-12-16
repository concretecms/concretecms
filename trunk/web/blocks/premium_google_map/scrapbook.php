<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<? if(strlen($controller->title) ){ ?>
<div style="margin-bottom:8px">Name: <?= $controller->title ?></div>
<? } ?>
<div>KML File: <?=($controller->kml_fID)? $controller->getFileURL() : 'None' ?></div>