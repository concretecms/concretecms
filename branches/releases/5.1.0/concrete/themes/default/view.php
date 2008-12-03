<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$this->inc('elements/header.php'); ?>


	<div id="central">
		<div id="sidebar">
			<?php 
			$as = new Area('Sidebar');
			$as->display($c);
			?>		
		</div>
		
		<div id="body">	
			<?php 

			print $innerContent;
			
			?>
		</div>
		
		<div class="spacer">&nbsp;</div>		
	</div>

<?php  $this->inc('elements/footer.php'); ?>