<?php  global $c; ?>

<a name="_edit<?php echo $b->getBlockID()?>"></a>

<?php  include(DIR_FILES_ELEMENTS_CORE . '/block_al.php'); ?>

<?php  $ci = Loader::helper("concrete/urls"); ?>
<?php  $bt = $b->getBlockTypeObject(); ?>
<?php  $url = $ci->getBlockTypeJavaScriptURL($bt); 
if ($url != '') { ?>
	<script type="text/javascript" src="<?php echo $url?>"></script>
<?php  } ?>
<form method="post" id="ccm-block-form" class="validate" action="<?php echo $b->getBlockEditAction()?>">



<div id="ccm-block-fields">
