<?
defined('C5_EXECUTE') or die(_("Access Denied."));

$this->inc('elements/header.php'); ?>


	<div id="central">
		<div id="sidebar">
			<?
			$as = new Area('Sidebar');
			$as->display($c);
			?>		
		</div>
		
		<div id="body">	
			<?

			print $innerContent;
			
			?>
		</div>
		
		<div class="spacer">&nbsp;</div>		
	</div>

<? $this->inc('elements/footer.php'); ?>