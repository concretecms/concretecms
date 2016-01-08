<?php
defined('C5_EXECUTE') or die("Access Denied.");
if (isset($error) && $error != '') {
	if ($error instanceof Exception) {
		$_error[] = $error->getMessage();
	} else if ($error instanceof \Concrete\Core\Error\Error) {
		$_error = $error->getList();
	} else if (is_array($error)) {
		$_error = $error;
	} else if (is_string($error)) {
		$_error[] = $error;
	}
	?>

	<?php if ($format == 'block' && count($_error) > 0) { ?>
	
	<div class="ccm-system-errors alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert">×</button>
	<?php foreach($_error as $e) { ?>
		<?php echo $e?><br/>
	<?php } ?>
	</div>

	<?php } else if (count($_error) > 0) { ?>
	
	<ul class="ccm-system-errors ccm-error">
	<?php foreach($_error as $e) { ?>
		<li><?php echo $e?></li>
	<?php } ?>
	</ul>
	<?php } ?>
	

<?php } ?>

<?php if (isset($message)) { ?>

	<div class="alert alert-info"><a data-dismiss="alert" href="#" class="close">&times;</a> <?=$message?></div>

<?php }

if (isset($success)) { ?>

	<div class="alert alert-success"><a data-dismiss="alert" href="#" class="close">&times;</a> <?=$success?></div>

<?php } ?>
