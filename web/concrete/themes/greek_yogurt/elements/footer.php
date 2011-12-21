<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="clear"></div>
	
	<div id="footer">
	
		<div id="footer-inner">
		
			<p class="footer-sign-in"><a href="<?php echo $this->url('/login')?>"><?php echo t('Sign In')?></a> <?php echo t(' to Edit this Site')?></p>
			<div class="clear"></div>
			<p class="footer-copyright">&copy;<?php echo date('Y')?> <?php echo SITE?>.</p>
			<p class="footer-tag-line">Built with <a href="http://www.concrete5.org/" alt="Free Content Management System" target="_blank">concrete5 - an open source CMS</a></p>
	
		</div>
	
	</div>

<!-- end main container -->

</div>

<?php  Loader::element('footer_required'); ?>

</body>
</html>