<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

	<div class="clear"></div>

	<div id="main-content-container" class="grid_24">
		<div id="main-content-inner">
			<? 
			Loader::element('system_errors', array('error' => $error));
			print $innerContent;
			?>
			
		</div>
	
	</div>
	
	<!-- end full width content area -->
	
<?php $this->inc('elements/footer.php'); ?>
