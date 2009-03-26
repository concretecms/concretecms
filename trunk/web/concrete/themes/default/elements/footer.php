<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
	<div id="footer">
			<span class="powered-by">This site is powered by <a href="http://www.concrete5.org">concrete5</a></span>
			<? 
			//$block = Block::getGlobalBlock('Standard Footer');
			//$block->display();
			?>
			&copy; <?=date('Y')?> <a href="<?=DIR_REL?>/"><?=SITE?></a>.
			&nbsp;&nbsp;
			<?=t('All rights reserved.')?>	
			<span class="sign-in"><a href="<?=$this->url('/login')?>"><?=t('Sign In to Edit this Site')?></a></span>
            
	</div>

</div>

<? Loader::element('footer_required'); ?>

</body>
</html>