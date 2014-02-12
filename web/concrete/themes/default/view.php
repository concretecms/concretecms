<?
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>


	<div id="central" class="no-sidebar">
		
		<div id="body">	
			<?
			Loader::element('system_errors', array('error' => $error));
			print $innerContent;			
			?>
		</div>
		
		<div class="spacer">&nbsp;</div>		
	</div>

<? $this->inc('elements/footer.php'); ?>