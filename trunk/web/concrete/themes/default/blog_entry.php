<?
defined('C5_EXECUTE') or die("Access Denied.");
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
			$ai = new Area('Entry Info');
			$ai->display($c);
			
			$a = new Area('Main');
			$a->display($c);
			
			$am = new Area('Entry More');
			$am->display($c);
			
			$ac = new Area('Entry Comments');
			$ac->display($c);			
			?>
		</div>
		
		<div class="spacer">&nbsp;</div>		
	</div>

<? $this->inc('elements/footer.php'); ?>