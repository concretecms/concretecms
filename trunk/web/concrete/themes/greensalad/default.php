<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$this->inc('elements/header.php'); ?>
<div id="page" class="sidebar-left">
	<div id="headerSpacer"></div>
	<div id="header">		
		<h1 id="logo"><a href="<?=DIR_REL?>/"><?=SITE?></a></h1>
		<?
		// we use the "is edit mode" check because, in edit mode, the bottom of the area overlaps the item below it, because
		// we're using absolute positioning. So in edit mode we add a bit of space so everything looks nice.
		if (!$c->isEditMode()) { ?>
			<div class="spacer"></div>
		<? } ?>		
		<div id="header-area">
			<?
			$a = new Area('Header Nav');
			$a->display($c);
			?>
		</div>
	</div>			
    <div id="central">
        <div id="pageHeader">
        <?			
        $ahh = new Area('Header');
        $ahh->display($c);			
        ?>	
        </div>
        <div id="sidebar">
			<?
			$as = new Area('Sidebar');
			$as->display($c);
			?>		
		</div>
		<div id="body">
			<?
			$a = new Area('Main');
			$a->display($c);
			?>
		</div>	
		<div class="spacer">&nbsp;</div>		
	</div>
	<div id="footer">
			&copy; <?=date('Y')?> <a href="<?=DIR_REL?>/"><?=SITE?></a>.
			&nbsp;&nbsp;
			All rights reserved.	
			<span class="sign-in"><a href="<?=$this->url('/login')?>">Sign In to Edit this Site</a></span>
	</div>
</div>
<? $this->inc('elements/footer.php'); ?>