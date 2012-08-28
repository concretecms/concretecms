<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

if (isset($error) && $error != '') {
	if ($error instanceof Exception) {
		$_error[] = $error->getMessage();
	} else if ($error instanceof ValidationErrorHelper) { 
		$_error = $error->getList();
	} else if (is_array($error)) {
		$_error = $error;
	} else if (is_string($error)) {
		$_error[] = $error;
	}
	?>
	<? if ($format == 'block') { ?>
	
	<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>
	<?php foreach($_error as $e): ?>
		<?php echo $e?><br/>
	<?php endforeach; ?>
	</div>

	<? } else { ?>
	
	<ul class="ccm-error">
	<?php foreach($_error as $e): ?>
		<li><?php echo $e?></li>
	<?php endforeach; ?>
	</ul>
	<? } ?>
	

<?php } ?>
