<?
defined('C5_EXECUTE') or die(_("Access Denied."));
global $c; ?>

<a name="_edit<?=$b->getBlockID()?>"></a>

<? $ci = Loader::helper("concrete/urls"); ?>
<? $bt = $b->getBlockTypeObject(); ?>
<? $url = $ci->getBlockTypeJavaScriptURL($bt); 
if ($url != '') { ?>
	<script type="text/javascript" src="<?=$url?>"></script>
<? } ?>
<form method="post" id="ccm-block-form" class="validate" action="<?=$b->getBlockEditAction()?>&rcID=<?=intval($rcID)?>" enctype="multipart/form-data">

<? foreach($this->controller->getJavaScriptStrings() as $key => $val) { ?>
	<input type="hidden" name="ccm-string-<?=$key?>" value="<?=$val?>" />
<? } ?>


<div id="ccm-block-fields">
