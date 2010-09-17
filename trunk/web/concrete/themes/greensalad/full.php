<?
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
<div id="page" class="no-sidebar">
	<div id="headerSpacer"></div>
	<div id="header">		
		<h1 id="logo"><!--
			--><a href="<?=DIR_REL?>/"><?
				$block = Block::getByName('My_Site_Name');
				if( $block && $block->bID ) $block->display();  
				else echo SITE;
			?></a><!--
		--></h1>
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
    <div id="pageHeader">
		<?			
        $ahh = new Area('Header');
        $ahh->display($c);			
        ?>	
    </div>

    <div id="central">
		<div id="body">	
			<?
			$a = new Area('Main');
			$a->display($c);
			?>
		</div>	
		<div class="spacer">&nbsp;</div>		
	</div>
	<div id="footer">
			<span class="powered-by"><a href="http://www.concrete5.org" title="<?=t('concrete5 - open source content management system for PHP and MySQL')?>"><?=t('concrete5 - open source CMS')?></a></span>
			&copy; <?=date('Y')?> <a href="<?=DIR_REL?>/"><?=SITE?></a>.
			&nbsp;&nbsp;
			<?=t('All rights reserved.')?>	
			<?
			$u = new User();
			if ($u->isRegistered()) { ?>
				<? 
				if (Config::get("ENABLE_USER_PROFILES")) {
					$userName = '<a href="' . $this->url('/profile') . '">' . $u->getUserName() . '</a>';
				} else {
					$userName = $u->getUserName();
				}
				?>
				<span class="sign-in"><?=t('Currently logged in as <b>%s</b>.', $userName)?> <a href="<?=$this->url('/login', 'logout')?>"><?=t('Sign Out')?></a></span>
			<? } else { ?>
				<span class="sign-in"><a href="<?=$this->url('/login')?>"><?=t('Sign In to Edit this Site')?></a></span>
			<? } ?>
	</div>
</div>
<? $this->inc('elements/footer.php'); ?>