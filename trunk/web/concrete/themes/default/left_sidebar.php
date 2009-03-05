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
			$ab = Block::getByName('my_picture');
			$ab->display();
			

			$a = new Area('Main');
			$a->display($c);			
			?>
		</div>
		
		<div class="spacer">&nbsp;</div>		
	</div>

<? $this->inc('elements/footer.php'); ?>