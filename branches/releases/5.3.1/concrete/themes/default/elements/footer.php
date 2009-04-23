<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
	<div id="footer">
			<span class="powered-by"><?php echo t('Built with ')?> <a href="http://www.concrete5.org"><?php echo t('concrete5 CMS') ?></a>.</span>
			<?php  
			//$block = Block::getGlobalBlock('Standard Footer');
			//$block->display();
			?>
			&copy; <?php echo date('Y')?> <a href="<?php echo DIR_REL?>/"><?php echo SITE?></a>.
			&nbsp;&nbsp;
			<?php echo t('All rights reserved.')?>	
			<span class="sign-in"><a href="<?php echo $this->url('/login')?>"><?php echo t('Sign In to Edit this Site')?></a></span>
            
	</div>

</div>

<?php  Loader::element('footer_required'); ?>

</body>
</html>