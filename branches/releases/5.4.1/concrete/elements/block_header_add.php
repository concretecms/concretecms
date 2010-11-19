<?php 
defined('C5_EXECUTE') or die("Access Denied.");
if ($action == null) { 
	// we can pass an action from the block, but in most instances we won't, we'll use the default
	$action = $bt->getBlockAddAction($a);
	global $c;
} ?>

<a name="_add<?php echo $bt->getBlockTypeID()?>"></a>

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
		ccm_setupBlockForm($(this), false, 'add');
	});
});

</script>

<input type="hidden" name="ccm-block-pane-action" value="<?php echo $_SERVER['REQUEST_URI']?>" />

<form method="post" action="<?php echo $action?>" class="validate" id="ccm-block-form" enctype="multipart/form-data">

<input type="hidden" name="ccm-block-form-method" value="REGULAR" />

<?php  foreach($this->controller->getJavaScriptStrings() as $key => $val) { ?>
	<input type="hidden" name="ccm-string-<?php echo $key?>" value="<?php echo $val?>" />
<?php  } ?>

<div id="ccm-block-fields">