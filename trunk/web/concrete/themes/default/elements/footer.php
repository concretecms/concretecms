<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
	<div id="footer">
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

<? require(DIR_FILES_ELEMENTS_CORE . '/footer_required.php'); ?>
</body>
</html>