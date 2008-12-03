<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$this->inc('elements/header.php'); ?>
<div id="page" class="sidebar-left">
	<div id="headerSpacer"></div>
	<div id="header">		
		<h1 id="logo"><a href="<?php echo DIR_REL?>/"><?php echo SITE?></a></h1>
		<?php 
		// we use the "is edit mode" check because, in edit mode, the bottom of the area overlaps the item below it, because
		// we're using absolute positioning. So in edit mode we add a bit of space so everything looks nice.
		if (!$c->isEditMode()) { ?>
			<div class="spacer"></div>
		<?php  } ?>		
		<div id="header-area">
			<?php 
			$a = new Area('Header Nav');
			$a->display($c);
			?>
		</div>
	</div>			
    <div id="central">
        <div id="pageHeader">
        <?php 			
        $ahh = new Area('Header');
        $ahh->display($c);			
        ?>	
        </div>
        <div id="sidebar">
			<?php 
			$as = new Area('Sidebar');
			$as->display($c);
			?>		
		</div>
		<div id="body">
			<?php 
			$a = new Area('Main');
			$a->display($c);
			?>
		</div>	
		<div class="spacer">&nbsp;</div>		
	</div>
	<div id="footer">
			&copy; <?php echo date('Y')?> <a href="<?php echo DIR_REL?>/"><?php echo SITE?></a>.
			&nbsp;&nbsp;
			<?php echo t('All rights reserved.')?>	
			<span class="sign-in"><a href="<?php echo $this->url('/login')?>"><?php echo t('Sign In to Edit this Site')?></a></span>
	</div>
</div>
<?php  $this->inc('elements/footer.php'); ?>