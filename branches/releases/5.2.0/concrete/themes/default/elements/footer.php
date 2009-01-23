<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
	<div id="footer">
			&copy; <?php echo date('Y')?> <a href="<?php echo DIR_REL?>/"><?php echo SITE?></a>.
			&nbsp;&nbsp;
			<?php echo t('All rights reserved.')?>	
			<span class="sign-in"><a href="<?php echo $this->url('/login')?>"><?php echo t('Sign In to Edit this Site')?></a></span>
	</div>

</div>

<?php  require(DIR_FILES_ELEMENTS_CORE . '/footer_required.php'); ?>
</body>
</html>