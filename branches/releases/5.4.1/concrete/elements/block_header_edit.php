<?php 
defined('C5_EXECUTE') or die("Access Denied.");
global $c; ?>

<a name="_edit<?php echo $b->getBlockID()?>"></a>

<?php  $bt = $b->getBlockTypeObject(); ?>

<script type="text/javascript">

<?php  $ci = Loader::helper("concrete/urls"); ?>
<?php  $url = $ci->getBlockTypeJavaScriptURL($bt); 
if ($url != '') { ?>
	ccm_addHeaderItem("<?php echo $url?>", 'JAVASCRIPT');
<?php  } 

$identifier = strtoupper('BLOCK_CONTROLLER_' . $btHandle);
if (is_array($headerItems[$identifier])) {
	foreach($headerItems[$identifier] as $item) { 
		if ($item instanceof CSSOutputObject) {
			$type = 'CSS';
		} else {
			$type = 'JAVASCRIPT';
		}
		?>
		ccm_addHeaderItem("<?php echo $item->file?>", '<?php echo $type?>');
	<?php 
	}
}
?>
$(function() {
	$('#ccm-block-form').each(function() {
		ccm_setupBlockForm($(this), '<?php echo $b->getBlockID()?>', 'edit');
	});
});
</script>

<form method="post" id="ccm-block-form" class="validate" action="<?php echo $b->getBlockEditAction()?>&rcID=<?php echo intval($rcID)?>" enctype="multipart/form-data">

<input type="hidden" name="ccm-block-form-method" value="REGULAR" />

<?php  foreach($this->controller->getJavaScriptStrings() as $key => $val) { ?>
	<input type="hidden" name="ccm-string-<?php echo $key?>" value="<?php echo $val?>" />
<?php  } ?>


<div id="ccm-block-fields">
