<?php defined('C5_EXECUTE') or die('Access denied.') ?>
<?php if (isset($message)) { ?>
	<div class="alert alert-success" role="alert"><?php echo $message ?></div>
<?php } ?>
<?php if ($loggedin) { ?>
	<div class="form-group">
		<p><?php echo t('You are currently logged in.'); ?></p>
		<p><?php echo t('Do you wish to link your Twitter account?') ?> </p>
		<hr>
	</div>
	<button class="btn btn-block btn-success authTwitterLogin"><?php echo t('Link with my Twitter account') ?></button>
<?php } else { ?>
	<button class="btn btn-block btn-success authTwitterLogin"><?php echo t('Log in with Twitter') ?></button>
<?php } ?>
<script type="text/javascript">
	$('button.authTwitterLogin').click(function() {
		var login = window.open('<?php echo $loginUrl ?>', 'Log in with Twitter', 'width=500,height=300');
		(login.focus && login.focus());

		function loginStatus() {
			if (login.closed) {
				window.location.href = '<?php echo $statusURI ?>';
				return;
			}
			setTimeout(loginStatus, 500);
		}

		loginStatus();
		return false;
	});
</script>
