<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
<div id="page" class="no-sidebar">
	<div id="headerSpacer"></div>
	<div id="header">		
		<h1 id="logo"><!--
			--><a href="<?php echo DIR_REL?>/"><?php 
				$block = Block::getByName('My_Site_Name');
				if( $block && $block->bID ) $block->display();  
				else echo SITE;
			?></a><!--
		--></h1>
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
    <div id="pageHeader">
		<?php 			
        $ahh = new Area('Header');
        $ahh->display($c);			
        ?>	
    </div>

    <div id="central">
		<div id="body">	
			<?php 
			$a = new Area('Main');
			$a->display($c);
			?>
		</div>	
		<div class="spacer">&nbsp;</div>		
	</div>
	<div id="footer">
			<span class="powered-by"><a href="http://www.concrete5.org" title="<?php echo t('concrete5 - open source content management system for PHP and MySQL')?>"><?php echo t('concrete5 - open source CMS')?></a></span>
			&copy; <?php echo date('Y')?> <a href="<?php echo DIR_REL?>/"><?php echo SITE?></a>.
			&nbsp;&nbsp;
			<?php echo t('All rights reserved.')?>	
			<?php 
			$u = new User();
			if ($u->isRegistered()) { ?>
				<?php  
				if (Config::get("ENABLE_USER_PROFILES")) {
					$userName = '<a href="' . $this->url('/profile') . '">' . $u->getUserName() . '</a>';
				} else {
					$userName = $u->getUserName();
				}
				?>
				<span class="sign-in"><?php echo t('Currently logged in as <b>%s</b>.', $userName)?> <a href="<?php echo $this->url('/login', 'logout')?>"><?php echo t('Sign Out')?></a></span>
			<?php  } else { ?>
				<span class="sign-in"><a href="<?php echo $this->url('/login')?>"><?php echo t('Sign In to Edit this Site')?></a></span>
			<?php  } ?>
	</div>
</div>
<?php  $this->inc('elements/footer.php'); ?>