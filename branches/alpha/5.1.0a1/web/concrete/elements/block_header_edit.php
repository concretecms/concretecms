<? global $c; ?>

<a name="_edit<?=$b->getBlockID()?>"></a>

<? include(DIR_FILES_ELEMENTS_CORE . '/block_al.php'); ?>

<? $ci = Loader::helper("concrete/urls"); ?>
<? $bt = $b->getBlockTypeObject(); ?>
<? $url = $ci->getBlockTypeJavaScriptURL($bt); 
if ($url != '') { ?>
	<script type="text/javascript" src="<?=$url?>"></script>
<? } ?>
<form method="post" id="ccm-block-form" class="validate" action="<?=$b->getBlockEditAction()?>">



<div id="ccm-block-fields">
