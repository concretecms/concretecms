<? defined('C5_EXECUTE') or die("Access Denied."); ?>
	<div id="footer">
			<span class="powered-by"><a href="http://www.concrete5.org" title="<?=t('concrete5 - open source content management system for PHP and MySQL')?>"><?=t('concrete5 - open source CMS')?></a></span>
			&copy; <?=date('Y')?> <a href="<?=DIR_REL?>/"><?=h(SITE)?></a>.
			&nbsp;&nbsp;
			<?=t('All rights reserved.')?>
			<?
			$u = new User();
			if ($u->isRegistered()) { ?>
				<?
				if (Config::get("concrete.user.profiles_enabled")) {
					$userName = '<a href="' . $view->url('/account/profile/public_profile') . '">' . $u->getUserName() . '</a>';
				} else {
					$userName = $u->getUserName();
				}
				?>
				<span class="sign-in"><?=t('Currently logged in as <b>%s</b>.', $userName)?> <a href="<?=$view->url('/login', 'logout', Loader::helper('validation/token')->generate('logout'))?>"><?=t('Sign Out')?></a></span>
			<? } else { ?>
				<span class="sign-in"><a href="<?=$view->url('/login')?>"><?=t('Sign In to Edit this Site')?></a></span>
			<? } ?>

	</div>

</div>

<? Loader::element('footer_required'); ?>

</body>
</html>
