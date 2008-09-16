<? if ($action == null) { 
	// we can pass an action from the block, but in most instances we won't, we'll use the default
	$action = $bt->getBlockAddAction($a);
	global $c;
} ?>

<a name="_add<?=$bt->getBlockTypeID()?>"></a>

<? include(DIR_FILES_ELEMENTS_CORE . '/block_al.php'); ?>

<? $ci = Loader::helper("concrete/urls"); ?>
<? $url = $ci->getBlockTypeJavaScriptURL($bt); 
if ($url != '') { ?>
	<script type="text/javascript" src="<?=$url?>"></script>
<? } ?>

<input type="hidden" name="ccm-block-pane-action" value="<?=$_SERVER['REQUEST_URI']?>" />

<form method="post" action="<?=$action?>" class="validate" id="ccm-block-form">

<div id="ccm-block-fields">