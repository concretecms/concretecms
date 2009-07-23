<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
global $c; ?>

<a name="_edit<?php echo $b->getBlockID()?>"></a>

<?php  $ci = Loader::helper("concrete/urls"); ?>
<?php  $bt = $b->getBlockTypeObject(); ?>
<?php  $url = $ci->getBlockTypeJavaScriptURL($bt); 
if ($url != '') { ?>
	<script type="text/javascript" src="<?php echo $url?>"></script>
<?php  } ?>
<form method="post" id="ccm-block-form" class="validate" action="<?php echo $b->getBlockEditAction()?>&rcID=<?php echo intval($rcID)?>" enctype="multipart/form-data">

<?php  foreach($this->controller->getJavaScriptStrings() as $key => $val) { ?>
	<input type="hidden" name="ccm-string-<?php echo $key?>" value="<?php echo $val?>" />
<?php  } ?>


<div id="ccm-block-fields">
