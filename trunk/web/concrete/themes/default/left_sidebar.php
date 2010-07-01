<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$this->inc('elements/header.php'); ?>


	<div id="central" class="central-left">
		<div id="sidebar">
			<?
			$as = new Area('Sidebar');
			$as->display($c);
			?>		
		</div>
		
		<div id="body">	
			<?
			$b = Block::getByName("prlist");
			$b->display();
			
			$a = new Area('Main');
			$a->display($c);			
			?>
		</div>
		
		<div class="spacer">&nbsp;</div>		
	</div>

<? $this->inc('elements/footer.php'); ?>