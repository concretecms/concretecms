<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
if ($action == null) { 
	// we can pass an action from the block, but in most instances we won't, we'll use the default
	$action = $bt->getBlockAddAction($a);
	global $c;
} ?>

<a name="_add<?php echo $bt->getBlockTypeID()?>"></a>

<?php  include(DIR_FILES_ELEMENTS_CORE . '/block_al.php'); ?>

<?php  $ci = Loader::helper("concrete/urls"); ?>
<?php  $url = $ci->getBlockTypeJavaScriptURL($bt); 
if ($url != '') { ?>
	<script type="text/javascript" src="<?php echo $url?>"></script>
<?php  } ?>

<input type="hidden" name="ccm-block-pane-action" value="<?php echo $_SERVER['REQUEST_URI']?>" />

<form method="post" action="<?php echo $action?>" class="validate" id="ccm-block-form" enctype="multipart/form-data">

<?php  foreach($this->controller->getJavaScriptStrings() as $key => $val) { ?>
	<input type="hidden" name="ccm-string-<?php echo $key?>" value="<?php echo $val?>" />
<?php  } ?>

<div id="ccm-block-fields">